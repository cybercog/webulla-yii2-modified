<?php
/**
 * Created by PhpStorm.
 * User: supreme
 * Date: 26.04.14
 * Time: 19:40
 */

namespace wbl\modified\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\helpers\Url;

class ModifiedWidget extends Widget {

	/**
	 * Модель для которой будет производиться проверка.
	 * @var \yii\db\ActiveRecord
	 */
	public $model;

	/**
	 * Атрибут, по которому будет производиться сравнение.
	 * @var
	 */
	public $attribute = 'updated_at';

	/**
	 * Адрес запроса.
	 * @var array
	 */
	public $url = ['view', 'id' => null];


	/**
	 * @inheritdoc
	 */
	public function run() {

		// собираем ссылку
		$url = Url::toRoute(array_merge($this->url, [
			'id' => $this->model->id
		]));

		// собираем временную метку
		$updated_at = $this->model->__get($this->attribute);
		$time = strtotime($updated_at);

		// собираем
		$alert = $this->renderAlert();

		// регистрируем скрипт
		Yii::$app->view->registerJs('
			/**
			 * Выполняет парсинг временной метки mysql.
			 */
			window.strtotime = function(string) {
				var t = string.split(/[- :]/);
				return Date.UTC(t[0], parseInt(t[1]) - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0) / 1000;
			};

			(function() {
				// сохраняем старую временную метку
				var timeOld = ' . json_encode($time) . ', timeNew;

				// создаем алерт
				var $alert = $(' . json_encode($alert) . ')
					.css("opacity", 0)
					.appendTo(\'body\');

				// [событие] фокус на окне браузера
				$(window).focus(function() {
					$.get(' . json_encode($url) . ', null, function(response) {
						timeNew = strtotime(response.updated_at);
						timeOld && timeNew > timeOld && $alert.css("opacity", 1);
						timeOld = timeNew;
					}, "json");
				});
			})();
		');

		return '';
	}

	/**
	 * Выполняем комплиряцию алерта.
	 * @return string
	 */
	public function renderAlert() {
		return Alert::widget([
			'options' => [
				'class' => 'fixed fixed-bottom alert alert-danger fade',
			],
			'body' => '
				<h4>Страница была изменена!</h4>
				<p>Данные, размещенные на этой странице, были кем-то изменены и теперь не являются актуальными. Пожалуйста, обновите страницу.</p>
				<p><a href="' . Yii::$app->request->url . '" class="btn btn-danger">Обновить страницу</a></p>'
		]);
	}
}