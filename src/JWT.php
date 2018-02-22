<?php
namespace Plokko\LaravelFirebase;


use Plokko\Firebase\ServiceAccount;

class JWT
{
    /**
     * @var ServiceAccount
     */
    private $serviceAccount;

    function __construct(ServiceAccount $serviceAccount)
    {
        $this->serviceAccount = $serviceAccount;
    }

    /**
     * @param $uid
     * @param array $claims
     */
    function encode($uid,array $claims=[]){
        return $this->serviceAccount->encodeJWT($uid,$claims);
    }
}