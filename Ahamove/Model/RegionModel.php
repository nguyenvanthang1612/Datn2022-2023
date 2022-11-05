<?php


namespace Magenest\Ahamove\Model;


class RegionModel
{
    const HANOI_SOURCE_CODE  = 'hanoi';
    const DANANG_SOURCE_CODE = 'danang';
    const HCMC_SOURCE_CODE   = 'hcmc';
    const CANTHO_SOURCE_CODE = 'cantho';

    const REGION_SOURCE_CITY = [
        self::HANOI_SOURCE_CODE  => '01',
        self::DANANG_SOURCE_CODE => '48',
        self::HCMC_SOURCE_CODE   => '79',
        self::CANTHO_SOURCE_CODE => '92'
    ];
}
