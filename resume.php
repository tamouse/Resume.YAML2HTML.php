<?php
/**
 *
 * resume - format resume in yaml format into html
 *
 * Author: Tamara Temple <tamara@tamaratemple.com>
 * Created: 2011/10/14
 * Copyright (c) 2011 Tamara Temple Web Development
 *
 */

require_once("lib/spyc.php"); // load the yaml converter
require_once("lib/class.Debug.php"); // load up the debugging messanger
$dbg = new Debug(TRUE);
$dbg->hold(TRUE); //hold messages until requested

if (php_sapi_name() === 'cli') {
  fill_request_with_arg();
  //$dbg->nohtml(TRUE);
}
$dbg->p("REQUEST Array: ", $_REQUEST, __FILE__, __LINE__ );


$file=get_request('file','str');
$Data = Spyc::YAMLLoad($file);
$dbg->p("YAML Structure of \$Data: ", $Data, __FILE__,__LINE__);

$accentcolour = 'DarkCyan';


/* ********************
 * FUNCTION DEFINITIONS
 * ******************** */


/**
 * fill the $_REQUEST array with the parm=value elements of $arg
 *
 * @return void
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function fill_request_with_arg()
{
  global $argv;
  array_walk($argv, create_function('$v,$k','
if (FALSE !== strpos($v,"=")) {
  list($key, $value) = explode("=",$v);
  $_REQUEST[$key] = $value;
}'));
}


/**
 * get the value specified in the REQUEST array
 *
 * @param string - key to examine and return
 * @param string - type of parameter
 * @return string - contents of $_REQUEST['file']
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function get_request($k,$type='str')
{
  if (isset($_REQUEST[$k]) &&
      !empty($_REQUEST[$k])) {
    switch ($type) {
    case 'str':
      if (is_string($_REQUEST[$k])) {
	$v = (string) $_REQUEST[$k];
      }
      break;

    case 'int':
      if (is_numeric($_REQUEST[$k])) {
	$v = (int) $_REQUEST[$k];
      }
      break;
	
    default:
      $v = $_REQUEST[$k];
      break;
    }
  } else {
    $v = FALSE;
  }
  return $v;
}



/**
 * wrap the given text with the HTML tag, adding attributes if given
 *
 * @param string $text - text to wrap
 * @param string $tag - HTML tag to wrap text in (optional, defaults to 'p')
 * @param array $attr - array of HTML attributes to add to tag opening (optional, defaults to empty)
 * @return string - wrapped text
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function _wrap($text, $tag='p', $attr=NULL)
{
  $w = '<'.$tag;
  if (is_array($attr)) {
    while(list($key,$value) = each($attr)) {
      $w .= " $key=\"$value\"";
    }
  }
  $w .= '>'.$text.'</'.$tag.'>';
  return $w;
}

/**
 * create the non-closure HTML tag, adding attributes
 *
 * @param string $tag - HTML tag to prepare
 * @param array $attr - attributes to add to HTML string
 * @return string - HTML tag string
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function _mktag($tag,$attr)
{
  if (!is_array($attr)) {
    return FALSE; // user MUST submit an array
  }

  $t = '<'.$tag;
  while(list($key,$value) = each($attr)) {
    $t .= " $key=\"$value\"";
  }
  $w .= ' />';
  return $w;
}

/**
 * format an array into set of list items
 *
 * @param array $list - the list of items to format
 * @param array (optional) $attr - an array of attributes to add to each list item
 * @return string - formatted list
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function _fmt_list($list,$attr=NULL)
{
  global $dbg;
  $dbg->p("in ".__FUNCTION__." showing \$list: ",$list,__FILE__,__LINE__);
  if (!is_array($list)) return FALSE;
  reset($list);

  $out='';
  while(list($key,$val) = each ($list)) {
    $dbg->p("In while loop in ".__FUNCTION__." showing \$key: ",$key,__FILE__,__LINE__);
    $dbg->p("In while loop in ".__FUNCTION__." showing \$val: ",$val,__FILE__,__LINE__);
    $out .= _wrap($val,'li',$attr) . PHP_EOL;
  }
  return $out;
}

/**
 * format info for an organization
 *
 * @return string - formatted string
 * @author Tamara Temple <tamara@tamaratemple.com>
 **/
function _fmt_organization($a)
{
  if (!is_array($a)) {
    return FALSE;
  }
  $out = '';
  while(list($k,$v)=each($a)) {
    $out .= _wrap($v,'span',array('class'=>$k)) . PHP_EOL;
  }
  return $out;
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	  "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta name="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $Data['Title'] ?></title>
    <meta name="generator" content="resume.php" />
    <meta name="robots" content="nofollow,index" />
    <style type="text/css" media="screen">
body { font-family: serif;  width: 85%; margin: 0px auto; background: background-color: #DDD; }
h1 { font-family: sans-serif; color: <?php echo $accentcolour ?>; }
h2 { font-family: sans-serif: color: <?php echo $accentcolour ?>; font-style: italic; }

#name { font-family: sans-serif; color: black; font-size: 300%; margin: 0;}

UL.nostyle { list-style: none; }
.alignleft { text-align: left; }
.alignright { text-align: right; }
.accentcolour {  color: <?php echo $accentcolour ?>; }

.textindent { margin-left: 3em; }

.skillslabel { font-style: italic; font-size: 80%; font-weight: bold; }

.years { width: 30em; margin-left: -5em; padding-right: 5em; display: inline; text-align: left }
.position { display: inline; font-weight: bold; }
.organization { display: inline; font-style: italic; }
.location { font-size: 90%; display: inline; }
.extra { font-size: 90%; display: inline; }

HR.leaderbar { width: 100%; color: <?php echo $accentcolour; ?>; height: 7px; background-color: <?php echo $accentcolour; ?>;}

#footer { text-align: center; font-size: 80%; font-style: italic: }

    </style>
  </head>
  <body>
    <table width="100%" cellspacing="1px" cellpadding="1px" border="0">
      <tr>
	<td valign="bottom" width="50%" align="left"><h1 id="name"><?php echo	$Data['Name']; ?></h1></td>
	<td valign="bottom" width="50%" align="right">
	<ul id="contactinfo" class="nostyle alignright">
  <?php $contact_info = array();
  $contact_info[] = $Data['Contact Info']['Address']['Street'].' Apt '.$Data['Contact Info']['Address']['Apartment'];
$contact_info[] = $Data['Contact Info']['Address']['City'] . ', ' .
  $Data['Contact Info']['Address']['State'] . ' ' .
  $Data['Contact Info']['Address']['Zip'];
$contact_info[] = $Data['Contact Info']['Phone'];
$contact_info[] = $Data['Contact Info']['Email'];
echo _fmt_list($contact_info,array('class'=>"accentcolour alignright"));
 ?>
	  </ul>
	</td>
      </tr>
    </table>

<table width="100%" cellspacing="3px" cellpadding="3px" border="0">
  <tr>
    <td width="200px"><hr class="leaderbar"></td>
    <td>
      <a name="Summary"></a>
      <h1>Summary</h1>
    </td>
  </tr>
  <tr>
    <td></td><td><p class="textindent"><?php echo $Data['Summary'];?></p>
</td>
  </tr>

  <tr>
    <td><hr class="leaderbar" /></td>
    <td>
      <a name="Skills"></a>
      <h1>Skills</h1>
    </td>
  </tr>
  <tr>
    <td></td>
    <td>
      <table width="75%" align="center" cellspacing="2px" cellpadding="2px" border="0">
	<tr valign="top">
	  <td align="right" class="skillslabel">Languages:</td>
	  <td><?php echo join(", ",$Data['Skills']['Languages'])?></td>
	  <td align="right" class="skillslabel">OSes:</td>
	  <td><?php echo join(", ",$Data['Skills']['OSes'])?></td>
	</tr>
	<tr valign="top">
	  <td align="right" class="skillslabel">Web Dev:</td>
	  <td><?php echo join(", ",$Data['Skills']['Web Dev'])?></td>
	  <td align="right" class="skillslabel">Web Design:</td>
	  <td><?php echo join(", ",$Data['Skills']['Web Design'])?></td>
	</tr>
	<tr valign="top">
	  <td align="right" class="skillslabel">Software:</td>
	  <td><?php echo join(", ",$Data['Skills']['Software']) ?></td>
	  <td align="right" class="skillslabel">Analysis:</td>
	  <td><?php echo join(", ",$Data['Skills']['Analysis']) ?></td>
	</tr>
	<tr valign="top">
	  <td align="right" class="skillslabel">Methods:</td>
	  <td colspan="3"><?php echo join("; ",$Data['Skills']['Methods']) ?></td>
	</tr>
      </table>
    </td>
  </tr>
  <tr>
    <td><hr class="leaderbar"></td>
    <td>
      <a name="Education"></a>
      <h1>Education</h1>
    </td>
  <tr>
    <td><?php echo _wrap($Data['Education']['University']['Years'].": ","p",array('class'=>"alignright")); ?></td>
    <td>
      <p>
	<?php echo _fmt_organization(array(
	      'position'=>$Data['Education']['University']['Degree'].",",
	'organization'=>$Data['Education']['University']['School'].",",
	'location'=>$Data['Education']['University']['Location'].",",
	'extra'=>'GPA: '.$Data['Education']['University']['GPA']));?>
      </p>
    </td>
  </tr>
  <tr>
    <td></td>
    <td>
      <h2>Additional Training:</h2>
    </td>
  </tr>
  <tr>
    <td></td>
    <td>
      <ul>
	<?php echo _fmt_list($Data['Education']['Additional Training']); ?>
      </ul>
    </td>
  </tr>
  </tr>

  <tr>
    <td><hr class="leaderbar" /></td>
    <td>
      <a name="Experience"></a>
      <h1>Experience:</h1>
    </td>
  </tr>
  <?php while (list($years,$job) = each($Data['Experience'])) { ?>
  <tr>
    <td valign="top">
        <a name="<?php echo $years?>"></a>
	<p class="alignright"><?php echo $years; ?>: </p>
    </td>
    <td>
      <p>
	<?php echo _fmt_organization(array(
	  'position'=>$job['Position'].",",
          'organization'=>$job['Company'].",",
          'location'=>$job['Location']))?>     
      </p>
      <p class="textindent"><?php echo $job['Description']?></p>
      <ul class="textindent">
	<?php echo _fmt_list($job['Accomplishments and Duties'])?>
      </ul>
    </td>

  </tr>
  <?php } //end while ?>

</table>




<div id="footer">
  Last updated: <?php echo $Data['Last Update']['Time-stamp']?>
</div>

    
<?php if ($dbg->is_on()) {
echo _wrap("Debug messages:",'h1');
echo "<pre class=\"debug\">".PHP_EOL;
print_r($dbg->getMessages());
echo "</pre>".PHP_EOL;
} ?>



  </body>
  
</html>
