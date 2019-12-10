<?php

namespace dokuwiki\plugin\dw2pdfextras;

use dokuwiki\Menu\Item\AbstractItem;

/**
 * Class MenuItem
 *
 * Implements the PDF namespace export button for DokuWiki's menu system
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @package dokuwiki\plugin\dw2pdfextras
 */
class MenuNSItem extends AbstractItem
{

    /** @var string do action for this plugin */
    protected $type = 'export_pdfns';

    /** @var string icon file */
    protected $svg = __DIR__ . '/../dw2pdf/file-pdf.svg';

    /**
     * MenuItem constructor.
     */
    public function __construct()
    {
        parent::__construct();
        global $REV, $DATE_AT, $ID;

        if ($DATE_AT) {
            $this->params['at'] = $DATE_AT;
        } elseif ($REV) {
            $this->params['rev'] = $REV;
        }

        $this->params['book_ns'] = getNS($ID);
        $this->params['book_title'] = noNS(getNS($ID));

        // set template parameter based on config
        $tpl = $this->getTemplate();
        if ($tpl) {
            $this->params['tpl'] = $tpl;
        }
    }

    /**
     * Get label from plugin language file
     *
     * @return string
     */
    public function getLabel()
    {
        $hlp = plugin_load('action', 'dw2pdfextras_button');
        return $hlp->getLang('export_pdfns_button');
    }

    /**
     * Template to be used for the current namespace, if configured
     *
     * @return string
     */
    protected function getTemplate()
    {
        global $ID;
        $template = '';
        $hlp = plugin_load('action', 'dw2pdfextras_button');
        $conf = $hlp->getConf('ns2template');

        if ($conf) {
            $lines = explode(',', $conf);
            foreach ($lines as $line) {
                list($ns, $tpl) = explode('|', $line);
                if (preg_match('/' . trim($ns) . '/', getNS($ID))) {
                    return trim($tpl);
                }
            }
        }

        return $template;
    }
}
