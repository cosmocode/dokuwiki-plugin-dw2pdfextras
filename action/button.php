<?php
/**
 * DokuWiki Plugin dw2pdfextras (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <andi@splitbrain.org>
 * @author  Anna Dabrowska <dokuwiki@cosmocode.de>
 */

class action_plugin_dw2pdfextras_button extends DokuWiki_Action_Plugin
{

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     *
     * @return void
     */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('TEMPLATE_PAGETOOLS_DISPLAY', 'FIXME', $this, 'addNSButton');
        $controller->register_hook('MENU_ITEMS_ASSEMBLY', 'AFTER', $this, 'addsvgbutton');
    }

    /**
     * [Custom event handler which performs action]
     *
     * Called for event:
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     *
     * @return void
     */
    public function addNSButton(Doku_Event $event, $param)
    {
        global $ID, $REV, $DATE_AT;

        if(!$event->data['view'] == 'main') {
            return;
        }

        if ($this->getConf('showexportnsbutton') && getNS($ID)) {
            $params = array('do' => 'export_pdf');
            if($DATE_AT) {
                $params['at'] = $DATE_AT;
            } elseif($REV) {
                $params['rev'] = $REV;
            }
            $params['book_ns'] = getNS($ID);
            $params['book_title'] = noNS(getNS($ID));
            // insert button at position before last (up to top)
            $event->data['items'] = array_slice($event->data['items'], 0, -1, true) +
                array('export_pdfns' =>
                    '<li>'
                    . '<a href="' . wl($ID, $params) . '"  class="action export_pdf" rel="nofollow" title="' . $this->getLang('export_pdfns_button') . '">'
                    . '<span>' . $this->getLang('export_pdfns_button') . '</span>'
                    . '</a>'
                    . '</li>'
                ) +
                array_slice($event->data['items'], -1, 1, true);
        }


    }

    /**
     * Add 'export pdf' button to page tools, new SVG based mechanism
     *
     * @param Doku_Event $event
     */
    public function addsvgbutton(Doku_Event $event)
    {
        global $INFO, $ID;

        if($event->data['view'] != 'page') {
            return;
        }

        if(!$INFO['exists']) {
            return;
        }

        if( $this->getConf('showexportnsbutton') && getNS($ID) ) {
            array_splice($event->data['items'], -1, 0, [new \dokuwiki\plugin\dw2pdfextras\MenuNSItem()]);
        }
    }
}
