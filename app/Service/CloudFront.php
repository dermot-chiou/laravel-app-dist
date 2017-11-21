<?php
/**
 * Created by PhpStorm.
 * User: dermot
 * Date: 2017/11/16
 * Time: 下午2:03
 */

namespace App\Service;


use Aws\CloudFront\CloudFrontClient;

class CloudFront
{
    private $client;
    private $distributionId;
    public function __construct()
    {
        if (config('disk.default') != 's3')
            return;
        $this->client = new CloudFrontClient([
            'version'     => 'latest',
            'region'      => env('AWS_REGION'),
            'credentials' => [
                'key'    => env('AWS_KEY'),
                'secret' => env('AWS_SECRET')
            ]
        ]);

        $this->distributionId = env('AWS_DISTRIBUTION_ID');
    }

    public function invalidate($items)
    {
        if (config('disk.default') != 's3')
            return;
        if (!is_array($items))
            $items = array($items);
        return $this->client->createInvalidation([
            'DistributionId' => $this->distributionId, // REQUIRED
            'InvalidationBatch' => [ // REQUIRED
                'CallerReference' => str_random(16), // REQUIRED
                'Paths' => [ // REQUIRED
                    'Items' => $items, // items or paths to invalidate
                    'Quantity' => count($items) // REQUIRED (must be equal to the number of 'Items' in the previus line)
                ]
            ]]);
    }
}