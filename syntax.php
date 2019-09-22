<?php
/**
 * DokuWiki Plugin yourip (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Artem Sidorenko <artem@2realities.com>
 * 2019-09 target urls changed by Hella Breitkopf, https://www.unixwitch.de
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'syntax.php';

class syntax_plugin_yourip extends DokuWiki_Syntax_Plugin {

    public function getType() {
        return 'substition';
    }

    public function getPType() {
        return 'block';
    }

    public function getSort() {
        return 99;
    }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~YOURIP_.*?~~',$mode,'plugin_yourip');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler){
        $data = array("yourip_type"=>"");
        $match = substr($match, 9, -2);

        if ($match == 'BOX')
            $data['yourip_type'] = 'box';
        elseif ($match == 'LINE')
            $data['yourip_type'] = 'line';
        elseif ($match == 'IPONLY')
            $data['yourip_type'] = 'iponlyline';

        return $data;
    }

    public function render($mode, Doku_Renderer $renderer, $data) {
        if($mode != 'xhtml') return false;

        $ip = getenv ("REMOTE_ADDR");
        $type=false;
        if (substr_count($ip,":") > 1 && substr_count($ip,".") == 0)
            $type='ipv6';
        else
            $type='ipv4';

        #show the things, here info in the box
        $text=false;
        if($data['yourip_type']=="box"){
            $text="\n<div id='yourip' class='$type'>";
            if($type=='ipv6')
                $text .= "You've got IPv6! <br/>IPv6 connection from $ip";
            else
                $text .= "You use old fashioned IPv4<br/>IPv4 connection from $ip";
            $text .="</div>\n";
            $renderer->doc .= $text;
            return true;

        #info as line
        }elseif($data['yourip_type']=="line"){
            $text="<p id='yourip' class='$type'>";
            if($type=='ipv6')
                $text .= "IPv6 connection from $ip";
            else
                $text .= "IPv4 connection from $ip";
            $text .="</p>\n";
            $renderer->doc .= $text;
            return true;

        #info without text
        }elseif($data['yourip_type']=="iponlyline"){
                $text = "<p id='yourip' class='$type'>";
                $text .= "$ip" ;
                $text .= "</p>\n" ; 
            $renderer->doc .= $text;
            return true;
        }
        else return false;

    } // end function render

} // end class

// vim:ts=4:sw=4:et:
