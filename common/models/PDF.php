<?php
namespace common\models;

use Yii;
Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));
require_once(Yii::getAlias('@anyname').'/vendor'.'/PDF'.'/fpdf.php');
class PDF extends \FPDF
{

    public $heading;
// Load data
    function LoadData($file)
    {
        // Read file lines
        $lines = file($file);
        $data = array();
        foreach($lines as $line)
            $data[] = explode(';',trim($line));
        return $data;
    }
    function WordWrap(&$text, $maxwidth)
    {
        $text = trim($text);
        if ($text==='')
            return 0;
        $space = $this->GetStringWidth(' ');
        $lines = explode("\n", $text);
        $text = '';
        $count = 0;

        foreach ($lines as $line)
        {
            $words = preg_split('/ +/', $line);
            $width = 0;

            foreach ($words as $word)
            {
                $wordwidth = $this->GetStringWidth($word);
                if ($wordwidth > $maxwidth)
                {
                    // Word is too long, we cut it
                    for($i=0; $i<strlen($word); $i++)
                    {
                        $wordwidth = $this->GetStringWidth(substr($word, $i, 1));
                        if($width + $wordwidth <= $maxwidth)
                        {
                            $width += $wordwidth;
                            $text .= substr($word, $i, 1);
                        }
                        else
                        {
                            $width = $wordwidth;
                            $text = rtrim($text)."\n".substr($word, $i, 1);
                            $count++;
                        }
                    }
                }
                elseif($width + $wordwidth <= $maxwidth)
                {
                    $width += $wordwidth + $space;
                    $text .= $word.' ';
                }
                else
                {
                    $width = $wordwidth + $space;
                    $text = rtrim($text)."\n".$word.' ';
                    $count++;
                }
            }
            $text = rtrim($text)."\n";
            $count++;
        }
        $text = rtrim($text);
        return $count;
    }
    function BasicTablePR($header, $data)
    {
        foreach($header as $col){
            $width = 19;
            if($col == 'Recovery Percentage'){
                $width = 24;
            }
            $this->Cell($width,6,$col,1);
        }
        $this->Ln();

         foreach($data as $key=>$row) {
             $width = 19;
             if($key == 'recovery_percentage'){
                 $width = 24;
             }
             $this->Cell($width, 6, $row, 1);
         }
        $this->Ln();
        $this->Ln();
    }
// Simple table
    function BasicTable($header, $data)
    {
        //print_r($header);
        $addressIndex =  array_search ( 'address' , $header );
        //print_r($header);
        //die("we die here");
        // Header
        foreach($header as $col){
            $width = 25;
            //$width = ($col == 'Address'  || $col == 'Name') ? 30 : 23;
            if($col == 'Address'){
                $width = 55;
            }
            else if($col == 'Name' || $col == 'Parentage'){
                $width = 30;
            }
            else if ($col == 'Sanct No'){
                $width = 20;
            }
            else if ($col == 'Disb Date' || $col == 'Due Date' || $col == 'Install #' || $col == 'Disb. Amount' || $col == 'Due Amount' || $col == 'Balance' || $col == 'Cum Recov'){
                $width = 15;
            }
            /*echo $key;
            echo $width;
            echo $addressIndex;
            die();*/
            $this->Cell($width,6,$col,1);
            //die($this->GetStringWidth('Muhammad Khalid Khan'));
            //$this->Cell($this->GetStringWidth($col)+3, 5, $col, 1);

        }
        $this->Ln();
        // Data
        /*foreach($data as $row)
        {
            foreach($row as $col)
                $this->Cell(23,6,$col,1);
                //$this->Cell($this->GetStringWidth($col)+3, 5, $col, 1);
                //$pdf->Cell($cellWidth + 5, 0, $string, 0, 0, 'C', true);
                $this->Ln();
        }*/

        foreach($data as $row) {

            //print_r($row);
            //die("we die here");
            $grpno = NULL;
            foreach($row as $row1){

                foreach($row1 as $key=>$col) {

                    $width = '25';
                    //print_r($col);
                    //die();
                    //$width = ($key == 'address' || $key == 'name') ? 30 : 23;

                    if($key == 'address'){
                        $width = 55;
                        $this->WordWrap($col ,50);
                        $this->Cell($width, 6, $col, 1);
                    }
                    else if ($key == 'name' || $key == 'parentage'){
                        $width = 30;
                        $this->Cell($width, 6, $col, 1);
                    }
                    else if ($key == 'sanction_no'){
                        $width = 20;
                        $this->Cell($width, 6, $col, 1);
                    }
                    else if ($key == 'date_disburse' || $key == 'due_date' || $key == 'install_no' || $key == 'dis_amount' || $key == 'due_amnt' || $key == 'balance' || $key == 'recv_amnt'){
                        $width = 15;
                        $this->Cell($width, 6, $col, 1);
                    }
                    else if($key == 'team' || $key == 'mobile'){
                        $width = 25;
                        $this->Cell($width, 6, $col, 1);
                    }
                    else if($key == 'grpno'){
                        // empty cell with left,top, and right borders
                        if($col != $grpno){
                            $this->Cell(290,6,$col,1);
                            $this->Ln();
                        }
                        $grpno = $col;
                    }

                    //$width = ('address' == $addressIndex) ? 40 : 23;
                    //echo $width;
                    //die($width);
                    //$this->Write(5,$col);
                    //$this->Cell($width, 6, $col, 1);
                }

                $this->Ln();
            }

        }
    }

// Better table
    function ImprovedTable($header, $data)
    {
        // Column widths
        $w = array(40, 35, 40, 45);
        // Header
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C');
        $this->Ln();
        // Data
        foreach($data as $row)
        {
            $this->Cell($w[0],6,$row[0],'LR');
            $this->Cell($w[1],6,$row[1],'LR');
            $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
            $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
            $this->Ln();
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
    }

// Colored table
    function FancyTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header
        $w = array(40, 35, 40, 45);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        foreach($data as $row)
        {
            $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
            $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
            $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
            $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
    }
    function Header()
    {
        // Logo
        //$this->Image('logo.png',10,6,30);
        // Arial bold 15
        $this->SetFont('Arial','B',10);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(0,5,$this->heading,0,0);
        // Line break
        $this->Ln(20);
    }
}

?>
