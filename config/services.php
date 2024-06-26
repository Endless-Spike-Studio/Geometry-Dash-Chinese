<?php

return [
	'endless' => [
		'proxy' => [
			'server' => env('ENDLESS_PROXY_SERVER'),
			'retry' => [
				'times' => env('ENDLESS_PROXY_RETRY_TIMES', 10),
				'delay' => env('ENDLESS_PROXY_RETRY_DELAY', 0)
			],
			'geometry_dash' => [
				'base' => env('ENDLESS_PROXY_GEOMETRY_DASH_BASE', 'https://www.boomlings.com/database')
			],
			'newgrounds' => [
				'audios' => [
					'storage' => [
						'disk' => 'oss',
						'format' => 'endless_proxy/newgrounds/audios/{id}.mp3'
					]
				]
			]
		]
	]
];