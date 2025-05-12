<?php

namespace App;

use Redis;

/**
 * Для простоты предположим что список стран фиксирован и уже нам известен.
 *
 * В противном случае стоило бы сделать асинхронное сохранение каждой страны в отдельную структуру в redis
 * Однако этого нельзя делать в рантайме т.к. вызовет большую нагрузку на единую стрктуру в которой этот список будет лежать.
 *
 * Я бы утащил в очередь и асинхронным процессом (кроном) обновлял бы, ну скажем собира все хиты за 10-20 секунд.
 *
 * Но в рамках задания не успею.
 */
class CountryListService {

    public function __construct(private Redis $redis)
    {
        $this->redis = $redis;
    }

    public function getCountries(): array
    {
        // TODO: утащить в асинхронно обновляемый отдельным воркером список

        return [
            'AU', 'AT', 'AZ', 'AL', 'DZ', 'AI', 'AO', 'AD', 'AQ', 'AG',
            'AN', 'AR', 'AM', 'AW', 'AF', 'BS', 'BD', 'BB', 'BH', 'BY',
            'BZ', 'BE', 'BJ', 'BM', 'BV', 'BG', 'BO', 'BA', 'BW', 'BR',
            'BN', 'BF', 'BI', 'BT', 'VU', 'VA', 'GB', 'HU', 'VE', 'VG',
            'VI', 'AS', 'TP', 'VN', 'GA', 'HT', 'GY', 'GM', 'GH', 'GP',
            'GT', 'GN', 'GW', 'DE', 'GI', 'HN', 'HK', 'GD', 'GL', 'GR',
            'GE', 'GU', 'DK', 'CD', 'DJ', 'DM', 'DO', 'EG', 'ZM', 'EH',
            'ZW', 'IL', 'IN', 'ID', 'JO', 'IQ', 'IR', 'IE', 'IS', 'ES',
            'IT', 'YE', 'CV', 'KZ', 'KY', 'KH', 'CM', 'CA', 'QA', 'KE',
            'CY', 'KG', 'KI', 'CN', 'CC', 'CO', 'KM', 'CG', 'CR', 'CI',
            'CU', 'KW', 'CK', 'LA', 'LV', 'LS', 'LR', 'LB', 'LY', 'LT',
            'LI', 'LU', 'MU', 'MR', 'MG', 'YT', 'MO', 'MK', 'MW', 'MY',
            'ML', 'MV', 'MT', 'MA', 'MQ', 'MH', 'MX', 'FM', 'MZ', 'MD',
            'MC', 'MN', 'MS', 'MM', 'NA', 'NR', 'NP', 'NE', 'NG', 'NL',
            'NI', 'NU', 'NZ', 'NC', 'NO', 'NF', 'AE', 'OM', 'PK', 'PW',
            'PS', 'PA', 'PG', 'PY', 'PE', 'PN', 'PL', 'PT', 'PR', 'RE',
            'CX', 'RU', 'RW', 'RO', 'SV', 'WS', 'SM', 'ST', 'SA', 'SZ',
            'SJ', 'SH', 'KP', 'MP', 'SC', 'VC', 'PM', 'SN', 'KN', 'LC',
            'SG', 'SY', 'SK', 'SI', 'US', 'SB', 'SO', 'SD', 'SR', 'SL',
            'TJ', 'TH', 'TW', 'TZ', 'TC', 'TG', 'TK', 'TO', 'TT', 'TV',
            'TN', 'TM', 'TR', 'UG', 'UZ', 'UA', 'WF', 'UY', 'FO', 'FJ',
            'PH', 'FI', 'FK', 'FR', 'GF', 'PF', 'HM', 'HR', 'CF', 'TD',
            'CZ', 'CL', 'CH', 'SE', 'LK', 'EC', 'GQ', 'ER', 'EE', 'ET',
            'YU', 'ZA', 'GS', 'KR', 'JM', 'JP', 'TF', 'IO', 'UM'
        ];
    }
}
