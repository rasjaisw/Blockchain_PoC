<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    var $data;

    function __construct() {
        parent::__construct();
		$this->load->library('form_builder');
        /*if (!$this->ion_auth->logged_in()) {
            redirect('Login');
        }
		if($this->ion_auth->is_admin()==FALSE){
			redirect('Applications');
		}*/

    }

	public function index() {
        $this->data['app']= "New";
		$this->template->load('index', 'home', $this->data);
    }
	function callAPI($method, $url, $data){
   $curl = curl_init();

   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
         break;
      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }

   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'APIKEY: 111111111111111111111',
      'Content-Type: application/json',
   ));
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}
	public function issue(){
		$this->load->library('form_validation');
       $arr = array("0001"=>"Accomplishment","0002"=>"Appreciation","0003"=>"Degree");
        $this->form_validation->set_rules('xx_first_name', 'Name', 'required|max_length[50]');
		#$this->form_validation->set_rules('db_email', 'Email', 'required|valid_email|max_length[100]');
          

        if ($this->form_validation->run() === FALSE) {

            

            $this->data['group'] = $arr;
            $this->template->load('index', 'add_certificate', $this->data);
        }
        else {
			#pre($this->input->post());
			$d=date(DATE_ISO8601);
			$dd=date('jS \of F Y');
			$s=sha1($d);
			$roster=array(
			'$class' => "org.degree.PersonalCertificate",
			"certId"=>$s,
			"localAdministrator"=> "resource:org.degree.Administrator#0571",
			"templateId"=> "resource:org.degree.CertificateTemplate#".$this->input->post('db_group_id'),
			"recipient"=>array(
						'$class' => "org.degree.Recipient",
						"hashed" => false,
						"email" => $this->input->post('db_email')
					),
			"recipientProfile"=> array(
						'$class' => "org.degree.RecipientProfile",
						"typen" => "RecipientProfile,Extension",
						"name" => $this->input->post('xx_first_name'),
						"publicKey"=> "Aute in Lorem veniam."
					),
					"issuer"=> array(
    '$class'=> "org.degree.Issuer",
    "id"=> "123",
    "typen"=> "Profile",
    "name"=> "Capgemini"
  ),
  "certifiedDate"=>$dd,
  "course"=>$this->input->post('course'),
			"hash"=> $s
  );
  #pre(json_encode($roster));
  $make_call = $this->callAPI('POST', 'http://localhost:3000/api/PersonalCertificate', json_encode($roster));
  $response = json_decode($make_call, true);
  #pre($response);
  if($response!=NULL){
	$this->data['response']=$response;
	$this->data['template']=$arr[$this->input->post('db_group_id')];
	$this->template->load('index', 'confirm_certificate', $this->data);
  }
  else{
	$this->template->load('index', 'error', $this->data);
  }

         /*   if ($this->ion_auth->register($this->input->post('db_username'), $this->input->post('db_password'), $this->input->post('db_email'), $additional_data, array($this->input->post('db_group_id')))) {
                redirect('Users');
            }*/
        }
	}
	
	public function verify(){
		$this->load->library('form_validation');
        $this->form_validation->set_rules('certid', 'ID', 'required|max_length[100]');
		#$this->form_validation->set_rules('db_email', 'Email', 'required|valid_email|max_length[100]');
        if ($this->form_validation->run() === FALSE) {

            $arr = array("0001"=>"Accomplishment","0002"=>"Appreciation","0003"=>"Degree");

            $this->data['group'] = $arr;
            $this->template->load('index', 'view', $this->data);
        }
        else {
           # $additional_data = parseForm($this->input->post(), 'xx_');
			$get_data = $this->callAPI('GET', 'http://localhost:3000/api/queries/verifyTransaction?certId='.$this->input->post('certid'),false);
			$response = json_decode($get_data, true);
			$this->data['response']=$response;
			if($response==NULL){
				$this->template->load('index', 'error', $this->data);
			}else{
				if($this->input->post('certid')==$response[0]['certId']){
					$this->data['v']="Verified";
				}
				else{
					$this->data['v']="Not Present";
				}
					$this->template->load('index', 'confirm', $this->data);
				
			}
		}
	}
	
	public function generate(){
		$this->load->library('form_validation');
        $this->form_validation->set_rules('certid', 'ID', 'required|max_length[100]');
		 $arr = array("0001"=>"Accomplishment","0002"=>"Appreciation","0003"=>"Degree");
		#$this->form_validation->set_rules('db_email', 'Email', 'required|valid_email|max_length[100]');
        if ($this->form_validation->run() === FALSE) {
            $this->data['group'] = $arr;
            $this->template->load('index', 'generate', $this->data);
        }
        else {
           # $additional_data = parseForm($this->input->post(), 'xx_');
			$get_data = $this->callAPI('GET', 'http://localhost:3000/api/queries/verifyTransaction?certId='.$this->input->post('certid'),false);
			$response = json_decode($get_data, true);
			$this->data['response']=$response;
			if($response==NULL){
				$this->template->load('index', 'error', $this->data);
			}else{
				if($this->input->post('certid')==$response[0]['certId']){
					$this->data['v']="Verified";
					$this->mypdf($response);
				}
			}
		}
	}
	
	 /*public function save_download($arr)
  { 
		//load mPDF library
		$this->load->library('m_pdf');
		//load mPDF library

		#pre($arr);exit;	
		//now pass the data//
		 $this->data['name']=$arr[0]['recipientProfile']['name'];
		 $this->data['time']= date('l jS \of F Y');
		 $this->data['hash']=$arr[0]['hash'];
		 //now pass the data //

		$html=$this->load->view('new',$this->data, true);
 	 
		//this the the PDF filename that user will get to download
		$pdfFilePath ="certificate.pdf";

		
		//actually, you can pass mPDF parameter on this load() function
		$pdf = $this->m_pdf->load();
		//generate the PDF!
		$pdf->WriteHTML($html,2);
		//offer it to user via browser download! (The PDF won't be saved on your server HDD)
		$pdf->Output($pdfFilePath, "D");
		 
		 	
  }*/
  
 public function mypdf($arr){
	$this->load->library('Pdf');
	$pdf = new Pdf('L', 'mm', 'A4', true, 'UTF-8', false);
	$pdf->SetTitle('Certificate');
	#$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->SetHeaderMargin(30);
	$pdf->SetTopMargin(20);
	$pdf->setFooterMargin(20);
	$pdf->SetAutoPageBreak(true);
	$pdf->SetAuthor('Author');
	$pdf->SetDisplayMode('real', 'default');
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->AddPage('L');
	$html = '<div align="center"><span style="color: rgb(0, 128, 128);">
	<h1>Certificate of Appreciation</h1>
	<br><br><br>
	<span style="font-size: large;">
		This is to certify that
		<br><br>
		<b><i>'.$arr[0]['recipientProfile']['name'].'</i></b>
	<br><br>
		has successfully completed the <br><br> <b>'.$arr[0]['course'].' course</b> <br><br>on<br><br><b>'.$arr[0]['certifiedDate'].'</b>
	<br>
	<br><br>
	<img src="images/Badge.jpg" alt="Badge" width="130" height="130" border="0" />
	<br><br>
	Issued by<br><b> '.$arr[0]['issuer']['name'].'</b>
	<br><br>
	Unique Identifier<br>'.$arr[0]['hash'].'
	<br>
	</span>

	</div>
	';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


	$pdf->Output('certificate.pdf', 'I');

 }

	
}
