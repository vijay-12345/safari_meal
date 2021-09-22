<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

trait Export
{
    private $export = true;

	private $url = null;

    private $headers = array();

    private $output = '';

    // Check if data is exportable?
    public function canExport()
    {
        return $this->export;
    }

    // Enable or disable export feature 
    public function setExport( $status )
    {
        $this->export = $status;
    }

    // Get export url
    public function getExportUrl()
    {
    	return $this->url;
    }

    // Set export url
    public function setExportUrl( $url )
    {
    	$this->url = $url;
    }

    // Get exported file headers (first row fields)
    public function getHeaders()
    {
        return $this->headers;
    }

    // Set exported file headers (first row fields)
    public function setHeaders(array $array)
    {
        $this->headers = $array;
    }

    // Get file name to be exported
    public function getFileName()
    {
        return $this->filename;
    }

    // Set file name to be exported
    public function setFileName( $name )
    {
        $name = $name ? $name : 'report';
        $this->filename = strtolower($name.'-'.date('d').'-'.date('M').'-'.date('Y').'-'.time().".xls");
    }

    // Get output to be written in exported file
    public function getOutput()
    {
        return $this->output;
    }

    // Set output to be written in exported file
    public function setOutput($output)
    {
        $this->output = $output();
    }

    // Get data to export
    public function getExportData( $model )
    {
        if( request('export') == 'all' ) {
            return $model->get()->toArray();
        }

        $take = request('per_page') ? (int) request('per_page') : $this->getPerPage();
        $page = request('page') ? (int) request('page') : 1;
        $skip = ($page - 1) * $take;
        
        return $model->skip( $skip )->take( $take )->get()->toArray();
    }

    // Override this function in your controller
    public function export()
    {
        //
    }

    // Finally export and download exported file
    public function exportFinally()
    {
        $filename = $this->getFileName();
        $output = implode("\t", $this->getHeaders())."\n". $this->getOutput();

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo $output;
        exit;
    }
}