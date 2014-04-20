<?php
require_once 'Zend/Http/Client.php';
define( 'APP_PATH', '/Users/anon/Sites/anoncom.net/application/modules/games' );
ini_set( 'INCLUDE_PATH', ini_get( 'INCLUDE_PATH' ) . PATH_SEPARATOR . APP_PATH );

$crawler = new AmusementCrawler;

$crawler->crawl();


class AmusementCrawler
{

	const BASE_URL = 'http://p.eagate.573.jp/game/facility/e-AMUSEMENT/p';
	
	public function crawl()
	{
		
		$client = new Zend_Http_Client;
		
		foreach ( $this->_getGameProducts() as $gameKey => $gameName )
		{
			
			foreach ( $this->_getPrefs() as $prefCode => $pref )
			{
				$path = '/result.html';
				do {
					$url = self::BASE_URL . $path;
					$client->setUri( $url );
					$params = [
						'finder'	=> 'area',
						'gkey'		=> $gameKey,
						'area'		=> '-1',
						'pref'		=> $prefCode,
					];
					$client->setParameterGet( $params );
					$response = $client->request();
					if ( $response->getStatus() == 200 ) {
						$result = $response->getBody();
						$result = mb_convert_encoding( $result, 'UTF-8', 'sjis-win' );
						$detailUrls = $this->_parseResult( $result );
						foreach ( $detailUrls as $detailUrl )
						{
							$url = self::BASE_URL . '/' . $detailUrl;
							$client->setUri($url);
							echo 'Request for ' . $url . PHP_EOL;
							$storeCode = $client->getUri()->getQueryAsArray()[ 'fcode' ];
							$response = $client->request();
							if ( $response->getStatus() == 200 ) {
								$detail = $response->getBody();
								$detail = mb_convert_encoding( $detail, 'UTF-8', 'sjis-win' );
								$datas = $this->_parseDetail( $detail );
								$datas[ 'store_code' ] = $storeCode;
								$datas[ 'pref_code' ] = $prefCode;
								$this->_registerStore( $datas );
							}
							
							
							sleep(1);
						}
					}
					sleep(1);
				} while(! $path = $this->_getNextpage( $result ) );
			}
		}
		
	}
	
	/**
	 * ゲームキー
	 * @return multitype:string
	 */
	private function _getGameProducts()
	{
		return [ 'SDVX'		=> 'SOUND VOLTEX' ];
		
		return [
			'GITADORADM'	=> 'GITADORA DrumMania',
			'GITADORAGF'	=> 'GITADORA GuitarFreaks',
			'JUBEATSAUCER'	=> 'jubeat',
// 			'COLORCOLOTTA'	=> 'カラコロッタ',
// 			'ORECA_PANDORA'	=> 'モンスター列伝オレカバトル　パンドラのメダル',
// 			'GOTOCHI_AROUND'	=> 'ご当地あラウンド',
			'QMA9'	=> 'クイズマジックアカデミー',
// 			'WEAC2014ALL'	=> 'Winning Eleven AC 2014',
// 			'WEAC2014WIDE'	=> 'Winning Eleven AC 2014(新筺体)',
// 			'WEAC2014STD'	=> 'Winning Eleven AC 2014(ｽﾀﾝﾀﾞｰﾄﾞ筺体)',
// 			'dreamsphere'	=> 'DreamSphere GRANDCROSS',
// 			'MFCU'	=> '麻雀格闘倶楽部',
// 			'MFCU_STD'	=> '麻雀格闘倶楽部(ｽﾀﾝﾀﾞｰﾄﾞﾓﾃﾞﾙ)',
// 			'MFCU_NEW'	=> '麻雀格闘倶楽部(ﾆｭｰｷｬﾋﾞﾈｯﾄ機能搭載ﾓﾃﾞﾙ)',
			'SC1'	=> 'Steel Chronicle VICTROOPERS',
			'IIDX'	=> 'beatmania IIDX',
// 			'EKLEGEND'	=> 'エターナルナイツLEGEND',
			'FUTURETOMTOM'	=> 'ミライダガッキ -FutureTomTom-',
			'SDVX'	=> 'SOUND VOLTEX',
			'BBHPOP'	=> 'BASEBALL HEROES 2013',
// 			'FLIPSP'	=> 'ﾌﾘｯﾌﾟｽﾊﾟｲﾗﾙ',
// 			'MILLIONET2'	=> 'ミリオネット2',
			'DDR'	=> 'DanceDanceRevolution',
// 			'ANIMAL2'	=> 'ｱﾆﾏﾛｯﾀ2',
			'PMSP'	=> 'pop\'n music Sunny Park',
// 			'GIGD'	=> 'GI-GranDesire',
// 			'FTRI2'	=> 'フォーチュントリニティ2',
			'REFLECC'	=> 'REFLEC BEAT colette',
// 			'SPINFEVER3'	=> 'SPINFEVER 夢幻のオーケストラ',
// 			'METEOSPARK'	=> 'ＭＥＴＥＯＳＰＡＲＫ',
// 			'WONDERMARCH'	=> 'WONDERMARCH',
			'DANEVOAC'	=> 'DanceEvolution ARCADE',
			'PCHARGER'	=> 'PASELIチャージ機',
// 			'VENUSFOUNTAIN'	=> 'Venus Fountain',
// 			'SHOGI2'	=> '天下一将棋会2',
// 			'WEAC2012ALL'	=> 'Winning Eleven AC 2012',
// 			'WEAC2012WIDE'	=> 'Winning Eleven AC 2012(新筺体)',
// 			'WEAC2012STD'	=> 'Winning Eleven AC 2012(ｽﾀﾝﾀﾞｰﾄﾞ筺体)',
// 			'GIHPJM'	=> 'GI-HORSEPARK JUDGMENT',
// 			'GIHPJM_STD'	=> 'GI-HORSEPARK JUDGMENT STD',
// 			'LPAC'	=> 'ﾗﾌﾞﾌﾟﾗｽｱｰｹｰﾄﾞ ｶﾗﾌﾙClip',
			'hpm'	=> 'ﾊﾛｰ!ﾎﾟｯﾌﾟﾝﾐｭｰｼﾞｯｸ',
// 			'LPM'	=> 'ﾗﾌﾞﾌﾟﾗｽ MEDAL Happy Daily Life',
// 			'ANIMAL'	=> 'ｱﾆﾏﾛｯﾀ',
// 			'GCC'	=> 'GRANDCROSS CHRONICLE',
// 			'PFAN'	=> 'ﾊﾟﾉﾗﾏﾌｧﾝﾀｼﾞｰ',
//			'MGA'	=> 'METAL GEAR ARCADE',
// 			'RF'	=> 'ﾛｰﾄﾞﾌｧｲﾀｰｽﾞ',
// 			'GITTV'	=> 'GI-Turf TV',
// 			'FTRI'	=> 'ﾌｫｰﾁｭﾝﾄﾘﾆﾃｨ',
			'ONPARA'	=> 'ｵﾝｶﾞｸﾊﾟﾗﾀﾞｲｽ',
// 			'IRINGS'	=> 'InfinityRings',
// 			'TBB'	=> 'ｻﾞ★ﾋﾞｼﾊﾞｼ',
// 			'HR2'	=> 'HORSERIDERS2',
// 			'GASHA2'	=> '投球王国ｶﾞｼｬｰﾝおかわり!',
// 			'MILLIONET'	=> 'MILLIONET',
// 			'GTI'	=> 'GTIｸﾗﾌﾞ ｽｰﾊﾟｰﾐﾆ･ﾌｪｽﾀ!',
// 			'MOTORX'	=> 'MOTOR X',
// 			'CVM'	=> '悪魔城ﾄﾞﾗｷｭﾗ THE MEDAL',
// 			'FF3'	=> 'FantasicFever3 Twinkle Fairytale',
// 			'SHA'	=> 'SILENT HILL THE ARCADE',
// 			'TD'	=> 'TwinkleDrop',
// 			'PANIC'	=> 'PanicPirates',
// 			'PP'	=> 'PRECIOUS PARTY',
// 			'GITW3'	=> 'GI-TURFWILD3',
		];
	}
	
	
	private function _getPrefs()
	{
		return [
			1	=> '北海道',
			2	=> '青森県',
			3	=> '岩手県',
			4	=> '秋田県',
			5	=> '福島県',
			6	=> '宮城県',
			7	=> '山形県',
			8	=> '群馬県',
			9	=> '栃木県',
			10	=> '茨城県',
			11	=> '埼玉県',
			12	=> '東京都',
			13	=> '千葉県',
			14	=> '神奈川県',
			15	=> '長野県',
			16	=> '富山県',
			17	=> '石川県',
			18	=> '福井県',
			19	=> '新潟県',
			20	=> '山梨県',
			21	=> '静岡県',
			22	=> '愛知県',
			23	=> '三重県',
			24	=> '岐阜県',
			25	=> '大阪府',
			26	=> '京都府',
			27	=> '滋賀県',
			28	=> '奈良県',
			29	=> '和歌山県',
			30	=> '兵庫県',
			31	=> '鳥取県',
			32	=> '島根県',
			33	=> '岡山県',
			34	=> '広島県',
			35	=> '山口県',
			36	=> '高知県',
			37	=> '愛媛県',
			38	=> '香川県',
			39	=> '徳島県',
			40	=> '福岡県',
			41	=> '長崎県',
			42	=> '佐賀県',
			43	=> '熊本県',
			44	=> '宮崎県',
			45	=> '大分県',
			46	=> '鹿児島県',
			47	=> '沖縄県',
		];
	}
	
	/**
	 * ページをパースして
	 * 詳細ページURL一覧を取得する
	 * @param string $content
	 */
	private function _parseResult( $content )
	{
		//if ( preg_match_all( '@<div class="shopInfo">(.+?)</div>@', $content, $matches ) )
		if ( preg_match_all( '@<a href="(facilitydetails\.html\?.+?)">.+?</a>@', $content, $matches ) )
			return $matches[ 1 ];
		return [];
	}
	
	private function _getNextpage( $content )
	{
		if ( preg_match( '@<a href="/game/facility/e-AMUSEMENT/p/(result\.html\?[^"]+)" accesskey="#">次へ</a>@', $content, $matches ) )
			return $matches[ 1 ];
		return null;
	}
	
	private function _parseDetail( $content )
	{
		$data = [
			'paseli'	=> false,
			'name'		=> null,
			'open'		=> null,
			'close'		=> null,
			'address'	=> null,
			'tel'		=> null,
			'access'	=> null,
			'longitude'	=> null,
			'latitude'	=> null,
		];
		$data[ 'paseli' ] = ( preg_match( '@<img src="/game/facility/e-AMUSEMENT/p/img/ico_paseli.gif" alt="PASELI 対応">@', $content ) !== false );
		if ( preg_match( '@<span id="name">([^<]+)</span>@', $content, $matches ) )
			$data[ 'name' ] = htmlspecialchars_decode($matches[ 1 ]);
		
		if ( preg_match( '@<dt>営業時間</dt>\s+<dd>(\d{1,2}:\d{2})-[^\d]*(\d{1,2}:\d{2}).*</dd>@', $content, $matches )){
			$data[ 'open' ] = $matches[ 1 ];
			$data[ 'close' ] = $matches[ 2 ];
		}
		if ( preg_match( '@<span id="address">([^<]+)</span>@', $content, $matches ) )
			$data[ 'address' ] = $matches[ 1 ];
		if ( preg_match( '@<dt>電話番号</dt>\s+<dd>(\d+-\d+-\d+)</dd>@', $content, $matches ) )
			$data[ 'tel' ] = $matches[ 1 ];
		if ( preg_match( '@<dt>店舗へのアクセス</dt>\s+<dd>([^<]+)</dd>@', $content, $matches ) )
			$data[ 'access' ] = $matches[ 1 ];
		
		if ( preg_match( '@<input type="hidden" id="latitude" value="(\d{1,3}\.\d+)" />\s+<input type="hidden" id="longitude" value="(\d{1,3}\.\d+)" />@', $content, $matches ) ) {
			$data[ 'latitude' ] = $matches[ 1 ];
			$data[ 'longitude' ] = $matches[ 2 ];
		}
		return $data;
	}
	
	private function _registerStore( array $datas )
	{
		/*
		require_once APP_PATH . '/models/Store.php';
		require_once APP_PATH . '/models/StoreMapper.php';
		require_once APP_PATH . '/models/Store/Location.php';
		require_once APP_PATH . '/models/Store/LocationMapper.php';
		require_once APP_PATH . '/models/Store/Products.php';
		require_once APP_PATH . '/models/Store/ProductsMapper.php';
		
		$store = new Games_Model_Store;
		$store
			->setStoreCode( $datas[ 'store_code' ] )
			->setName( $datas[ 'name' ] )
			->setPrefCode( $datas[ 'pref_code' ] )
			->setAddress( $datas[ 'address' ] )
			->setAccess( $datas[ 'access' ] )
			->setTimeOpen( $datas[ 'open' ] )
			->setTimeClose( $datas[ 'close' ] )
		;
		$mapper = new Games_Model_StoreMapper;
		$id = $mapper->save($store);
		
		$st_loc = new Games_Model_Store_Location;
		$st_loc
			->setStoreId( $id )
			->setLatitude( $datas[ 'latitude' ] )
			->setLongitude( $datas[ 'longitude' ] )
		;
		$mapper = new Games_Model_Store_LocationMapper;
		$mapper->save( $st_loc );
		
		$st_prod = new Games_Model_Store_Products;
		$st_prod
			->setProductId( 1 )
			->setNum( 1 )
			->setServiceEam( $datas[ 'paseli' ] ? 1 : 0 )
		;
		$mapper = new Games_Model_Store_ProductsMapper;
		$mapper->save( $st_prod );
		*/
		var_dump($datas);
	}
}

