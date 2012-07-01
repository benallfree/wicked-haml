<?

function eval_haml($path, $data=array(), $capture = false)
{

  if(!file_exists($path)) wicked_error("File $path does not exist for HAMLfication.");
  $unique_name = folderize(ftov($path));
  $php_path = HAML_CACHE_FPATH."/$unique_name.php";
  if (is_newer($path, $php_path))
  {
    haml_to_php($path, $php_path);
  }
  if(!file_exists($php_path)) dprint('wtf');

  return eval_php($php_path,$data,$capture);
}


function haml_to_php($src)
{
  global $__wicked;
  if(endswith($src, '.php')) return $src;
    
  $unique_name = folderize(ftov($src));
  $dst = HAML_CACHE_FPATH."/$unique_name.php";
  ensure_writable_folder(dirname($dst));
  if ($__wicked['modules']['haml']['always_generate'] == false && !is_newer($src, $dst)) return $dst;

  $lex = new HamlLexer();
  $lex->N = 0;
  $lex->data = file_get_contents($src);
  $s = $lex->render_to_string();
  file_put_contents($dst, $s);
  return $dst;
}

function str_to_haml($s)
{
  $lex = new HamlLexer();
  $lex->N = 0;
  $lex->data = $s;
  $s = $lex->render_to_string();
  return $s;
}

function generate_lexer()
{
  if (is_newer($parser_src,$parser_dst))
  {
    require_once 'LexerGenerator.php';
    ob_start();
    $lex = new PHP_LexerGenerator($parser_src);
    ob_get_clean();
  }
}