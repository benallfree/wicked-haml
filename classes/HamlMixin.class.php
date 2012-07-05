<?

class HamlMixin extends Mixin
{
  static $__prefix = 'haml';
  static $module = null;
  
  static function init()
  {
    parent::init();
    self::$module = W::module('haml');
  }
  
  static function eval_file($path, $data=array(), $capture = false)
  {
  
    if(!file_exists($path)) W::error("File $path does not exist for HAMLfication.");
    $unique_name = W::folderize(W::ftov($path));
    $php_path = self::$module['cache_fpath']."/$unique_name.php";
    if (W::is_newer($path, $php_path))
    {
      self::to_php($path, $php_path);
    }
    if(!file_exists($php_path)) dprint('wtf');
  
    return W::php_sandbox($php_path,$data,$capture);
  }
  
  
  static function to_php($src)
  {
    global $__wicked;
    if(W::endswith($src, '.php')) return $src;
      
    $unique_name = W::folderize(W::ftov($src));
    $dst = self::$module['cache_fpath']."/$unique_name.php";
    W::ensure_writable_folder(dirname($dst));
    if ($__wicked['modules']['haml']['always_generate'] == false && !W::is_newer($src, $dst)) return $dst;
  
    $lex = new HamlLexer();
    $lex->N = 0;
    $lex->data = file_get_contents($src);
    $s = $lex->render_to_string();
    file_put_contents($dst, $s);
    return $dst;
  }
  
  static function to_string($s)
  {
    $lex = new HamlLexer();
    $lex->N = 0;
    $lex->data = $s;
    $s = $lex->render_to_string();
    return $s;
  }
  
  static function generate_lexer()
  {
    if (W::is_newer($parser_src,$parser_dst))
    {
      require_once 'LexerGenerator.php';
      ob_start();
      $lex = new PHP_LexerGenerator($parser_src);
      ob_get_clean();
    }
  }
}