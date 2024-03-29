<?php

namespace App\Services;

use App\Models\Number;
use App\Nova\MmAfterNoonNumber;
use Illuminate\Support\Str;

class BetNumbers
{
    protected $numbers = [];

    /**
     * @param $numbers
     * @return $this
     */
    public function setData($numbers)
    {
        $rns = MmAfterNoonNumber::where('type', '2D')->pluck('number');
        foreach ($numbers as $number)
        {
            if (in_array($number['type'], ['D','R','F','E']))
            {
                foreach ($number['numbers'] as $i)
                {
                    switch ($number['type']) {
                        case 'D':
                            $this->addNumber($i, $number['type'], $number['amount']);
                            break;
                        case 'R':
                            $this->addNumber($i, $number['type'], $number['amount']);
                            $this->addNumber(strrev($i), $number['type'], $number['amount']);
                            break;
                        case 'F':
                            $filtered = $rns->filter(function ($value, $key) use ($i) {
                                return Str::of($value)->startsWith($i);
                            });
                            foreach ($filtered->all() as $f) {
                                $this->addNumber($f, $number['type'], $number['amount']);
                            }
                            break;
                        case 'E':
                            $filtered = $rns->filter(function ($value, $key) use ($i) {
                                return Str::of($value)->endsWith($i);
                            });
                            foreach ($filtered->all() as $f) {
                                $this->addNumber($f, $number['type'], $number['amount']);
                            }
                            break;
                    }
                }
            }else{
                switch ($number['type']) {
                    case '0BK':
                    case '1BK':
                    case '2BK':
                    case '3BK':
                    case '4BK':
                    case '5BK':
                    case '6BK':
                    case '7BK':
                    case '8BK':
                    case '9BK':
                    case 'POWER':
                    case 'NATKHAT':
                    case '10FULL':
                    case '5FULL':
                    case '0FULL':
                    case 'PADAYTAR':
                    case 'BROTHER':
                    case 'POWER_BROTHER':
                    case 'APUU':
                        $nums = config('essentials.numbers.' . $number['type']);
                        foreach ($nums as $n) {
                            $this->addNumber($n, $number['type'], $number['amount']);
                        }
                        break;
                    case 'POWER_BROTHER_R':
                        $nums = config('essentials.numbers.POWER_BROTHER');
                        foreach ($nums as $n) {
                            $this->addNumber($n, $number['type'], $number['amount']);
                            $this->addNumber(strrev($n), $number['type'], $number['amount']);
                        }
                        break;
                    case 'BROTHER_R':
                        $nums = config('essentials.numbers.BROTHER');
                        foreach ($nums as $n) {
                            $this->addNumber($n, $number['type'], $number['amount']);
                            $this->addNumber(strrev($n), $number['type'], $number['amount']);
                        }
                        break;
                }
            }

        }

        return $this;
    }

    /**
     * @param $number
     * @param $type
     * @param $amount
     */
    private function addNumber($number, $type, $amount)
    {
        $this->numbers[] = [
            'number' => $number,
            'type' => $type,
            'amount' => $amount,
        ];
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->numbers;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return collect($this->numbers)->sum('amount');
    }
}