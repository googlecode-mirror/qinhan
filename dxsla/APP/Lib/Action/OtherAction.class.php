<?php
class OtherAction extends CommonAction{
    public function about() {
		//dump(get_magic_quotes_gpc());
		$this->display();
	}
	 public function job() {
		$this->display();
	}
	 public function contact() {
		$this->display();
	}
	 public function kf(){
		$this->display();
	}
	 public function reg_agreement() {
		$this->display();
	}
	public function kf_opt() {
		$kf = M("kf");
		$data["message_type"]=intval($_POST["message_type"]);
		$data["message_content"]=$_POST["message_content"];
		$data["customer_link"]=$_POST["customer_link"];
		$data["pid"]=$_POST["pid"];
		$data["photo_uid"]=$_POST["photo_uid"];
		$data["report_addresses"]=$_POST["report_addresses"];
		//dump($data);
		$kf->add($data);
		//echo $kf->getLastSql();
		echo 1;		
	}
}
?>