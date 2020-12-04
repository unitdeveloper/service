<?php

namespace zetsoft\service\graph;


use zetsoft\service\graph\InputUserType;
use GraphQL\Type\Definition\Type;
use zetsoft\service\graph\QueryType;
use zetsoft\service\graph\UserType;

/**
 * Class Types
 *
 * Реестр и фабрика типов для GraphQL
 *
 * @package App
 */
class Types
{
    /**
     * @var QueryType
     */
    private static $query;

    /**
     * @var UserType
     */
    private static $user;

    /**
     * @var MutationType
     */
    private static $mutation;

    /**
     * @var InputUserType
     */
    private static $inputUser;
    /**
     * @return QueryType
     */
    public static function query()
    {
        return self::$query ?: (self::$query = new QueryType());
    }

    /**
     * @return UserType
     */
    public static function user()
    {
        return self::$user ?: (self::$user = new UserType());
    }

    /**
     * @return \GraphQL\Type\Definition\IntType
     */
    public static function int()
    {
        return Type::int();
    }

    /**
     * @return \GraphQL\Type\Definition\StringType
     */
    public static function string()
    {
        return Type::string();
    }

    /**
     * @param \GraphQL\Type\Definition\Type $type
     * @return \GraphQL\Type\Definition\ListOfType
     */
    public static function listOf($type)
    {
        return Type::listOf($type);
    }

    public static function mutation()
    {
        return self::$mutation ?: (self::$mutation = new MutationType());
    }

    public static function inputUser()
    {
        return self::$inputUser ?: (self::$inputUser = new InputUserType());
    }
}
