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

        $service_account_email=$this->serviceAccount->get;

        $now = time();
        $payload = [
            'iss'   => $service_account_email,
            'sub'   => $service_account_email,
            'aud'   => 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit',
            'iat'   => $now,
            'exp'   => $now+3600,
            'uid'   => $uid,
            'claims'=> $claims,
        ];
    }
}