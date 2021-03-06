<?php

namespace App\Helpers\WalletHandlers;
use App\Wallet;
use App\Currency;
use App\Balance;

class ManualWalletHandler extends WalletHandler {

	public $name = 'Manual';
	protected $fields = [
        'currency' => [
        	'type' => 'select',
        	'data' => 'getData'
        ],
        'value' => 'text',
	];
    public $validation = [
        // 'currency' => 'required|exists:currencies,id',
        'currency' => 'required|exists:currencies,symbol',
        'value' => 'required|regex:/^[\d]{0,8}.[\d]{0,8}$/'
    ];

	public function handle(Wallet $wallet)
	{
		$balance = $wallet->balancesOfSymbol($wallet->raw_data['currency']);

		if (is_null($balance)) {
			$balance = new Balance();
			$balance->wallet_id = $wallet->id;
			$balance->symbol = $wallet->raw_data['currency'];
		}

		$balance->value = $wallet->raw_data['value'];
		$balance->save();
	}

	public static function getData()
	{
		return array_map(function($currency) {
			return [
				'value' => $currency['symbol'],
				'label' => $currency['name']
			];
		}, Currency::all()->toArray());
	}
}
