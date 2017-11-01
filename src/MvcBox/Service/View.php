<?php
namespace MvcBox\Service;
// обработчик шаблонов в виде сервиса: подключение в виде сервиса в файле index.php, 
// вызывается метод рендера с передачей параметров, вьюшки, которую нужно сгенерить и лэйаута, в который вьюшка должна бысть встроена

class View {
  private $app = null;

  public function __construct($app) {
    $this->app = $app;
  }

  public function render( $layout, $template, $vars = array() ) {
    $path = __DIR__ . '/../Views';
	  
	extract($vars); // берет массив и каждый элемент делает переменной, в которую записывает её значение
    //foreach ($vars as $key => $value) { $$key = $value; }
	
    $app = $this->app;
    ob_start();// буферизация вывода

    require $path . '/' . $template;

    $content = ob_get_clean();   // достает данные из буфера, делает вывод и очищает буфер обмена
    
    if ( null == $layout ) {
      return $content;
    }
    
    ob_start();
    require_once $path . '/' . $layout;
    $html = ob_get_clean();

    return $html;
  }
    
}