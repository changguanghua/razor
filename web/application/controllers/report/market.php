<?php

class market extends CI_Controller{
	private $data = array();
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->Model('common');
		$this->load->model('channelmodel','channel');
		$this->load->model('product/productmodel','product');
		$this->load->model('product/newusermodel','newusermodel');
		$this->common->requireLogin();
		$this->common->requireProduct();
		
	}
	
	function viewMarket()
	{
		
		$product = $this->common->getCurrentProduct();
		$productId = $product->id;
		$data['productId'] = $productId;		
		$today = date ( 'Y-m-d', time () );
		$yestodayTime = date ( "Y-m-d", strtotime ( "-1 day" ) );
		$seven_day = date ( "Y-m-d", strtotime ( "-6 day" ) );
		$thirty_day = date ( "Y-m-d", strtotime ( "-30 day" ) );
				
		$sevendayactive=$this->product->getActiveUsersNum($seven_day,$today,$productId);
		$data['sevendayactive']=$sevendayactive;
		$thirty_day_active=$this->product->getActiveUsersNum($thirty_day,$today,$productId);
		$data['thirty_day_active']=$thirty_day_active;
		$todayData = $this->product->getAnalyzeDataByDateAndProductID($today,$productId);
	    $yestodayData = $this->product->getAnalyzeDataByDateAndProductID($yestodayTime,$productId);

	    $data['count'] = $todayData->num_rows();
		$data ['todayData'] = $todayData;
		$data['yestodayData']=$yestodayData;
		$this->common->loadHeaderWithDateControl ();
		
		$fromTime = $this->common->getFromTime ();
		$toTime = $this->common->getToTime ();		
		$data['reportTitle'] = array(
				'timePase' => getTimePhaseStr($fromTime, $toTime),
				'newUser'=>  getReportTitle(lang("v_rpt_mk_newUserStatistics")." ".null , $fromTime, $toTime),
				'activeUser'=> getReportTitle(lang("v_rpt_mk_activeuserS")." ".null , $fromTime, $toTime),
				'Session'=> getReportTitle(lang("v_rpt_mk_sessionS")." ". null, $fromTime, $toTime),
				'avgUsageDuration'=>  getReportTitle(lang("t_averageUsageDuration")." ".null , $fromTime, $toTime),
				'activeWeekly'=> getReportTitle(lang("t_activeRateW")." ".null , $fromTime, $toTime),
				'activeMonthly'=> getReportTitle(lang("t_activeRateM")." ". null, $fromTime, $toTime)
		);
		$this->load->view('overview/productmarket',$data);
		
	}
	
	function getMarketData($type='')
	{
		$product = $this->common->getCurrentProduct();
		$productId = $product->id;
		$fromTime = $this->common->getFromTime ();
		$toTime = $this->common->getToTime ();
		$markets = $this->product->getProductChanelById($productId);
		$ret = array();
		if($markets!=null && $markets->num_rows()>0)
		{
			foreach ($markets->result() as $row)
			{
				if($type=="monthrate"||$type=="weekrate")
				{
					$data = $this->	product->getActiveNumber($row->channel_id,$fromTime,$toTime,$type);
				}
				else
				{					
					$data = $this->product->getAllMarketData($row->channel_id,$fromTime,$toTime);
				}
			}
		}
		else
		{
			$data="";
		}		
		$result = json_encode($data);
		echo  $result;
		
	}
	
	
}

?>