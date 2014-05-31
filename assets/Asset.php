<?php
/**
 * Created by PhpStorm.
 * User: supreme
 * Date: 16.04.14
 * Time: 0:59
 */

namespace wbl\modified\assets;

use yii\web\AssetBundle;

class Asset extends AssetBundle {

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset',
	];
}