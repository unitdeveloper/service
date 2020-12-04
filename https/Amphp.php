<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 * https://github.com/stonemax/acme2
 */

namespace zetsoft\service\https;


use Amp\Loop;
use Amp\Http\Client\HttpClientBuilder;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;
use zetsoft\dbitem\http\AmPhpItem;
use zetsoft\system\kernels\ZFrame;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use Amp\Http\Client\HttpException;

use function Amp\File\open;
use function Amp\File\size;
use function Amp\getCurrentTime;
use Amp\File\File;
use Amp\File\StatCache;


class Amphp extends ZFrame
{
    #region Test
    public function test()
    {
       // $this->testSendGetRequest();
       $this->testSendRequestForLargeRespons();
    }

    private function testSendGetRequest()
    {
        $url = 'https://httpbin.org/user-agent';
        $this->getRequest($url);
    }

    private function testSendRequestForLargeRespons()
    {
        $item1 = new AmPhpItem();
        $item1->url = 'http://speed.hetzner.de/100MB.bin';
        $item1->path = Root . '/upload/AmPhp/file1.tmp';

        $item2 = new AmPhpItem();
        $item2->url = 'http://speed.hetzner.de/100MB.bin';
        $item2->path = Root . '/upload/AmPhp/file2.tmp';

        $item3 = new AmPhpItem();
        $item3->url = 'http://speed.hetzner.de/100MB.bin';
        $item3->path = Root . '/upload/AmPhp/file3.tmp';

        $item4 = new AmPhpItem();
        $item4->url = 'http://speed.hetzner.de/100MB.bin';
        $item4->path = Root . '/upload/AmPhp/file4.tmp';

        $items = [
           $item1,
           $item2,
           $item3,
           $item4,
        ];
        $this->getLargeRespons($items);
    }
    #endregion

    #region Vars

    #endregion

    #region main
    public function getRequest($url)
    {
        Loop::run(static function () use ($url) {
            try {
                // Instantiate the HTTP client
                $client = HttpClientBuilder::buildDefault();

                // Make an asynchronous HTTP request
                $promise = $client->request(new Request($url));

                // Client::request() is asynchronous! It doesn't return a response. Instead, it returns a promise to resolve the
                // response at some point in the future when we've received the headers of the response. Here we use yield which
                // pauses the execution of the current coroutine until the promise resolves. Amp will automatically continue the
                // coroutine then.
                /** @var Response $response */


                 /*$response = yield $promise;

                 dumpRequestTrace($response->getRequest());
                 dumpResponseTrace($response);

                 dumpResponseBodyPreview(yield $response->getBody()->buffer());*/

            } catch (HttpException $error) {
                // If something goes wrong Amp will throw the exception where the promise was yielded.
                // The HttpClient::request() method itself will never throw directly, but returns a promise.
                echo $error;
            }
        });

    }


    /**
     *
     * Function  getLargeRespons
     * @param AmPhpItem[] $items
     */
    public function getLargeRespons($items)
    {
        Loop::run(static function () use ($items): \Generator {
            try {
                $start = getCurrentTime();
               
                // Instantiate the HTTP client\
                $clients = [];
                foreach ($items as $key => $item)
                    $clients[$key] = HttpClientBuilder::buildDefault();

                $requests = [];
                foreach ($items as $key => $item)
                {
                    $requests[$key] = new Request($item->url);
                    $requests[$key]->setBodySizeLimit($item->SizeLimit * 1024 * 1024); // 128 MB
                    $requests[$key]->setTransferTimeout($item->time_out * 1000); // 120 seconds
                }


                // Make an asynchronous HTTP request
                $promises = [];
                foreach ($items as $key => $item)
                    $promises[$key] = $clients[$key]->request($requests[$key]);

                // Client::request() is asynchronous! It doesn't return a response. Instead, it returns a promise to resolve the
                // response at some point in the future when we've received the headers of the response. Here we use yield which
                // pauses the execution of the current coroutine until the promise resolves. Amp will automatically continue the
                // coroutine then.
                /** @var Response $response */
                $responses = [];
                foreach ($items as $key => $item)
                    $responses[$key] = yield $promises[$key];

                // Output the results
                foreach ($items as $key => $item)
                {
                    \printf(
                        "HTTP/%s %d %s\r\n%s\r\n\r\n",
                        $responses[$key]->getProtocolVersion(),
                        $responses[$key]->getStatus(),
                        $responses[$key]->getReason(),
                        (string) $responses[$key]->getRequest()->getUri()
                    );

                    foreach ($responses[$key]->getHeaders() as $field => $values) {
                        foreach ($values as $value) {
                            print "$field: $value\r\n";
                        }
                    }
                }

                print "\n";

                $paths = [];
                foreach ($items as $key => $item)
                    $paths[$key] = $item->path;

                /** @var File $file */

                $files = [];
                //vdd($paths);
                foreach ($items as $key => $item)
                    $files[$key] = yield open($paths[$key], "w");

                $bytes = [];
                foreach ($items as $key => $item)
                    $bytes[$key] = 0;

                // The response body is an instance of Payload, which allows buffering or streaming by the consumers choice.
                // We could also use Amp\ByteStream\pipe() here, but we want to show some progress.
                
                $is_exist_first_chunk = false;
                foreach ($items as $key => $item)
                    $is_exist_first_chunk = ($is_exist_first_chunk or (null !== yield $responses[$key]->getBody()->read()));

                $service = new Amphp();
                while ($is_exist_first_chunk) {
                    $is_all_continue = false;
                    $output_txt = '';
                    foreach ($items as $key => $item)
                    {
                        $chunk = yield $responses[$key]->getBody()->read();

                        $is_continue = false;
                        if($chunk !== null)
                        {
                            yield $files[$key]->write($chunk);

                            $bytes[$key] += \strlen($chunk);
                            $output_txt .= $service->formatBytes($bytes[$key]) . " ___ ";

                            $is_continue = true;
                        }
                        $is_all_continue = ($is_all_continue or $is_continue);
                    }

                    //$is_exist_first_chunk = $is_all_continue;
                    $is_exist_first_chunk = $is_all_continue;
                    print "\r" . $output_txt . '    '; // blanks to remove previous output
                }

                /*yield $file->close();

                print \sprintf(
                    "\rDone in %.2f seconds with peak memory usage of %.2fMB.\n",
                    (getCurrentTime() - $start) / 1000,
                    (float) \memory_get_peak_usage(true) / 1024 / 1024
                );

                // We need to clear the stat cache, as we have just written to the file
                StatCache::clear($path);
                $size = yield size($path);*/

                //print \sprintf("%s has a size of %.2fMB\r\n", $path, (float) $size / 1024 / 1024);
            } catch (HttpException $error) {
                // If something goes wrong Amp will throw the exception where the promise was yielded.
                // The HttpClient::request() method itself will never throw directly, but returns a promise.
                echo $error;
            }
        });
    }
    #endregion

    #region helpers
    private function formatBytes(int $size, int $precision = 2): string
    {
        $base = \log($size, 1024);
        $suffixes = ['bytes', 'kB', 'MB', 'GB', 'TB'];

        return \round(1024 ** ($base - \floor($base)), $precision) . ' ' . $suffixes[(int) $base];
    }
    #endregion

}
