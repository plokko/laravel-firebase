<?php

return [
    /**
     * Firebase service account information, can be either:
     * - string : absolute path to serviceaccount json file
     * - string : content of serviceaccount (json string)
     * - array : php array conversion of the serviceaccount
     */
    'service_account' => base_path('.serviceAccount.json'),
    /**
     * If set to true will enable Google OAuth2.0 token cache storage
     */
    'cache' => true,
    /**
     * Cache driver, if null default cache driver will be used
     * @var string|null
     */
    'cache_driver'=>null,
];
