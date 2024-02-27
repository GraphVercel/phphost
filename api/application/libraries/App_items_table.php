<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/App_items_table_template.php');

class App_items_table extends App_items_table_template
{
    public function __construct($transaction, $type, $for = 'html', $admin_preview = false)
    {
        // Required
        $this->type          = strtolower($type);
        $this->admin_preview = $admin_preview;
        $this->for           = $for;

        $this->set_transaction($transaction);
        $this->set_items($transaction->items);

        parent::__construct();
    }

    /**
     * Builds the actual table items rows preview
     * @return string
     */
    public function items()
    {
        $html = '';


        $descriptionItemWidth = 54;//$this->get_description_item_width();

        $regularItemWidth  = 10;//$this->get_regular_items_width(6);
        if($this->admin_preview == true){
            $customFieldsItems = $this->get_custom_fields_for_table();
        }
        

        if ($this->for == 'html') {
            $descriptionItemWidth = $descriptionItemWidth - 5;
            $regularItemWidth     = $regularItemWidth - 5;
        }

        $i = 1;
        foreach ($this->items as $item) {
            $itemHTML = '';

            // Open table row
            $itemHTML .= '<tr' . $this->tr_attributes($item) . '>';

            // Table data number
            $itemHTML .= '<td' . $this->td_attributes() . ' align="center" width="5%">' . $i . '</td>';

            $itemHTML .= '<td class="description" align="left;" width="' . $descriptionItemWidth . '%">';

            /**
             * Item description
             */
            if (!empty($item['description'])) {
                $itemHTML .= '<span style="font-size:' . $this->get_pdf_font_size() . 'px;"><strong>'
                . $this->period_merge_field($item['description'])
                . '</strong></span>';

                if (!empty($item['long_description'])) {
                    $itemHTML .= '<br />';
                }
            }

            
            $itemHTML .= '</td>';

            /**
             * Item custom fields
             */
            if($this->admin_preview == true){
                foreach ($customFieldsItems as $custom_field) {
                    if($custom_field['name'] == 'Quickbooks Product ID'){
                        continue;
                    }
                    $cf_value = get_custom_field_value($item['id'], $custom_field['id'], 'items');
                    if(preg_match_all('~<a href="([^"]++)"~',$cf_value,$m)){
                        
                        $itemHTML .= '<td align="left" width="' . $regularItemWidth . '%">' . $cf_value . '</td>';
                        
                    }else{

                        $itemHTML .= '<td align="left" width="' . $regularItemWidth . '%"><a href="'.$cf_value.'" target="_blank">' . $custom_field['name'] . '</a></td>';

                    }
                    
                }
            }
            

            /**
             * Item quantity
             */
            $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . floatVal($item['qty']);

            /**
             * Maybe item has added unit?
             */
            if ($item['unit']) {
                $itemHTML .= ' ' . $item['unit'];
            }


            $itemHTML .= '</td>';

            /**
             * Item rate
             * @var string
             */
            $rate = hooks()->apply_filters(
                'item_preview_rate',
                app_format_money($item['rate'], $this->transaction->currency_name, $this->exclude_currency()),
                ['item' => $item, 'transaction' => $this->transaction]
            );

           
            $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . $rate. '</td>';
            



            /**
             * Items table taxes HTML custom function because it's too general for all features/options
             * @var string
             */
            $itemHTML .= $this->taxes_html($item, $regularItemWidth);

            /**
             * Possible action hook user to include tax in item total amount calculated with the quantiy
             * eq Rate * QTY + TAXES APPLIED
             */
            $item_amount_with_quantity = hooks()->apply_filters(
                'item_preview_amount_with_currency',
                app_format_money(($item['qty'] * $item['rate']), $this->transaction->currency_name, $this->exclude_currency()),
                $item,
                $this->transaction,
                $this->exclude_currency()
            );

            $itemHTML .= '<td class="amount" align="right" width="' . $regularItemWidth . '%">' . $item_amount_with_quantity . '</td>';

            // Close table row
            $itemHTML .= '</tr>';
            // for html
            if ($this->for == 'html') {

                $itemHTML .= '<tr>';
                 $itemHTML .= '<td colspan="2">';
                 
                if (!empty($item['long_description'])) {
                    $itemHTML .= '<span style="color:#424242;">' . $this->period_merge_field($item['long_description']) . '</span>';
                }
                 $itemHTML .= '</td>';
                 $itemHTML .= '<td class="pdng_set" style="padding-left: 10px;" colspan="4" align="right" width="' . $regularItemWidth . '%">';
                 
                if (item_images_load($item['item_id'])) {
                    $imagesArray = item_images_load($item['item_id']);
                    $count = count($imagesArray);

                    for ($i = 0; $i < $count; $i++) {

                        if ($i % 2 == 0) {
                            $itemHTML .= '<span class="image-container" style="float:left;">';
                        }

                        $images = $imagesArray[$i]->image;
                        $itemHTML .= '<img class="preview-image" src="' . base_url() . 'assets/invoice_items/thumb/' . $images . '" style="margin-right: 10px;padding:8px;border:1px solid;width:185px;object-fit: contain" onclick="previewImage(this)">';

                        // Close the <div> container for every two images or if it's the last image
                        if ($i % 2 == 1 || $i == $count - 1) {
                            $itemHTML .= '</span>';
                        }
                    }
                }

                $itemHTML .= '</td></tr>';
            }
             
            // for pdf
            if ($this->for == 'pdf') {
                $itemHTML .='<tr>';
                $itemHTML .='<td colspan="2" rowspan="2">';
                if (!empty($item['long_description'])) {
                    $itemHTML .= '<span style="color:#424242;">' . $this->period_merge_field($item['long_description']) . '</span>';
                }
                $itemHTML .='</td>';
                if (item_images_load($item['item_id'])) {
                    $imagesArray = item_images_load($item['item_id']);
                    $count = count($imagesArray);
                    for ($i = 0; $i < $count; $i++) {
                        if($i<2){
                            $images = $imagesArray[$i]->image;
                            $itemHTML .='<td colspan="2" style="border:1px 0px 0px 1px solid black;text-align: center;vertical-align: middle;">';
                            $itemHTML .='<img src="' . base_url() . 'assets/invoice_items/thumb/' . $images . '">';
                            $itemHTML .='</td>';
                        }
                    }
                }
                $itemHTML .='</tr>';

                $itemHTML .='<tr>';
                if (item_images_load($item['item_id'])) {
                    $imagesArray = item_images_load($item['item_id']);
                    $count = count($imagesArray);
                    for ($i = 0; $i < $count; $i++) {
                        if($i>1 && $i<4){
                            $images = $imagesArray[$i]->image;
                            $itemHTML .='<td colspan="2" style="border:1px 0px 0px 1px solid black; text-align: center;vertical-align: middle;">';
                            $itemHTML .='<img src="' . base_url() . 'assets/invoice_items/thumb/' . $images . '">';
                            $itemHTML .='</td>';
                        }
                    }
                }
                $itemHTML .='</tr>';


                // $itemHTML .= '<tr>';
                //      $itemHTML .= '<td colspan="2">';
                     
                //         if (!empty($item['long_description'])) {
                //             $itemHTML .= '<span style="color:#424242;">' . $this->period_merge_field($item['long_description']) . '</span>';
                //         }
                //      $itemHTML .= '</td>';
                //  $itemHTML .= '</tr>';
                //  $itemHTML .= '<tr><td colspan="6">&nbsp;</td></tr>';
                //  $itemHTML .= '<tr>';
                //  $itemHTML .= '<td colspan="6" align="center">';
                 
                // if (item_images_load($item['item_id'])) {
                //     $imagesArray = item_images_load($item['item_id']);
                //     $count = count($imagesArray);

                //     for ($i = 0; $i < $count; $i++) {

                //         if ($i % 2 == 0) {
                //             $itemHTML .= '<span class="image-container" style=" padding:5px;">';
                //         }

                //         $images = $imagesArray[$i]->image;
                //         $itemHTML .= '<img class="preview-image" src="' . base_url() . 'assets/invoice_items/thumb/' . $images . '" width="180" height="140" onclick="previewImage(this)">&nbsp;&nbsp;';

                //         // Close the <div> container for every two images or if it's the last image
                //         if ($i % 2 == 1 || $i == $count - 1) {
                //             $itemHTML .= '</span>';
                //         }
                //     }
                // }

                // $itemHTML .= '</td>';
                //  $itemHTML .= '</tr>';
            }

            $html .= $itemHTML;

            $i++;
        }

        return $html;
    }

    /**
     * Html headings preview
     * @return string
     */
    public function html_headings()
    {
        $html = '<tr>';
        $html .= '<th align="center">' . $this->number_heading() . '</th>';
        $html .= '<th class="description" width="' . $this->get_description_item_width() . '%" align="left">' . $this->item_heading() . '</th>';
        if($this->admin_preview == true){
            $customFieldsItems = $this->get_custom_fields_for_table();
            foreach ($customFieldsItems as $cf) {
                if($cf['name'] == 'Quickbooks Product ID'){
                    continue;
                }
                $html .= '<th class="custom_field" align="left">' . $cf['name'] . '</th>';
            }
        }
        

        $html .= '<th align="right">' . $this->qty_heading() . '</th>';
        $html .= '<th align="right">' . $this->rate_heading() . '</th>';
        if ($this->show_tax_per_item()) {
            $html .= '<th align="right">' . $this->tax_heading() . '</th>';
        }
        $html .= '<th align="right">' . $this->amount_heading() . '</th>';
        $html .= '</tr>';

        return $html;
    }

    /**
     * PDF headings preview
     * @return string
     */
    public function pdf_headings()
    {
        $descriptionItemWidth = 54;//$this->get_description_item_width();
        $regularItemWidth     = 10;//$this->get_regular_items_width(6);
        // $customFieldsItems    = $this->get_custom_fields_for_table();

        $tblhtml = '<tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">';

        $tblhtml .= '<th width="5%;" align="center">' . $this->number_heading() . '</th>';
        $tblhtml .= '<th width="' . $descriptionItemWidth . '%" align="left">' . $this->item_heading() . '</th>';

        // foreach ($customFieldsItems as $cf) {
        //     $tblhtml .= '<th width="' . $regularItemWidth . '%" align="left">' . $cf['name'] . '</th>';
        // }

        $tblhtml .= '<th width="' . $regularItemWidth . '%" align="right">' . $this->qty_heading() . '</th>';
        $tblhtml .= '<th width="' . $regularItemWidth . '%" align="right">' . $this->rate_heading() . '</th>';

        if ($this->show_tax_per_item()) {
            $tblhtml .= '<th width="' . $regularItemWidth . '%" align="right">' . $this->tax_heading() . '</th>';
        }

        $tblhtml .= '<th width="' . $regularItemWidth . '%" align="right">' . $this->amount_heading() . '</th>';
        $tblhtml .= '</tr>';

        return $tblhtml;
    }

    /**
     * Check for period merge field for recurring invoices
     *
     * @return string
     */
    protected function period_merge_field($text)
    {
        if ($this->type != 'invoice') {
            return $text;
        }

        // Is subscription invoice
        if (!property_exists($this->transaction, 'recurring_type')) {
            return $text;
        }

        $startDate       = $this->transaction->date;
        $originalInvoice = $this->transaction->is_recurring_from ?
            $this->ci->invoices_model->get($this->transaction->is_recurring_from) :
            $this->transaction;

        if (!preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $startDate)) {
            $startDate = to_sql_date($startDate);
        }

        if ($originalInvoice->custom_recurring == 0) {
            $originalInvoice->recurring_type = 'month';
        }

        $nextDate = date('Y-m-d', strtotime(
            '+' . $originalInvoice->recurring . ' ' . strtoupper($originalInvoice->recurring_type),
            strtotime($startDate)
        ));

        return str_ireplace('{period}', _d($startDate) . ' - ' . _d(date('Y-m-d', strtotime('-1 day', strtotime($nextDate)))), $text);
    }

    protected function get_description_item_width()
    {
        $item_width = hooks()->apply_filters('item_description_td_width', 38);

        // If show item taxes is disabled in PDF we should increase the item width table heading
        return $this->show_tax_per_item() == 0 ? $item_width + 15 : $item_width;
    }

    protected function get_regular_items_width($adjustment)
    {
        $descriptionItemWidth = $this->get_description_item_width();
        $customFieldsItems    = $this->get_custom_fields_for_table();
        // Calculate headings width, in case there are custom fields for items
        $totalheadings = $this->show_tax_per_item() == 1 ? 4 : 3;
        $totalheadings += count($customFieldsItems);

        return (100 - ($descriptionItemWidth + $adjustment)) / $totalheadings;
    }
}