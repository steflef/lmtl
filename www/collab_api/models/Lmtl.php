<?php
//namespace Lmtl;
/**
 * Created by JetBrains PhpStorm.
 * User: prof_lebrun
 * Date: 13-01-23
 * Time: 3:17 PM
 * To change this template use File | Settings | File Templates.
 */
abstract class Lmtl
{
    public function __construct(){

    }

/*    public static function getUser( $db, $id=0){

        $select = $db->newSelect();
        $select ->cols(['*'])
                ->from('users')
                ->where('id = :id');

        $bind = ['id' => $id];
        return $db->fetchAll($select, $bind);
    }*/

    public static function checkUser( $db, $email='', $psw=''){

        $select = $db->newSelect();
        $select ->cols(['*'])
            ->from('users')
            ->where('email = :email')
            ->where('password = :psw');

        $bind = [
                    'email' => $email,
                    'psw'=> $psw
                ];
        $rs = $db->fetchAll($select, $bind);

        return $rs;
    }

    public static function getUser( $db, $email=''){

        $select = $db->newSelect();
        $select ->cols(['*'])
            ->from('users')
            ->where('email = :email');

        $bind = ['email' => $email];
        return $db->fetchAll($select, $bind);
    }

    public static function getUsers( $db ){

        $select = $db->newSelect();
        $select ->cols(['id,email,username,role'])
            ->from('users')
            ->orderBy(['role DESC']);

        return $db->fetchAll($select);
    }

    public static function isAdmin( $db, $email=''){

        $select = $db->newSelect();
        $select ->cols(['role'])
            ->from('users')
            ->where('email = :email')
            ->where('role = :role');

        $bind = [
            'email' => $email,
            'role' => 1
        ];

        return $db->fetchValue($select, $bind);
    }
}