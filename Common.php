<?php
class Common extends CI_Model {




//late coming count in month
public function lateComingCountInMonth($empid,$monthstart)
{
  if($monthstart >= date('d'))//we are    
  {
     $month = date('m')-1; 
     if($month == '12')
     {
         $y = date('Y')-1;
     }else{
         $y = date('Y');
     }
  }else{
      $month = date('m');
      $y = date('Y');
  }
  $monthstartd = $y.'-'.date($month.'-'.$monthstart);
  $daystotalinmonth = date('t',strtotime($monthstartd));//days in month
  $adddays = $daystotalinmonth -1; // -1 to get end date of month
        $s_date=$monthstartd;
        $e_date=date('Y-m-d',strtotime('+'.$adddays.' days',strtotime($monthstartd)));
        $where=array();
        $where['emp_id']=$empid;         
        $where['dates>=']=$s_date;
        $where['dates<=']=$e_date;
        $where['comming_reason!=']='';
        $data=$this->getData('attendance',$where,['attenance_id'=>'Asc'],'',0,'dates'); 
        return count($data);
}
public function getData($table="",$whr="",$ord="",$limit="",$first=0,$group_by="")
    {
		//print_r($ord); die;
	if(!empty($whr))
	$this->db->where($whr);
	if($group_by!='')
            { 
               $this->db->group_by($group_by);
            }
	// if(!empty($ord)){
   //      list($key, $value) = each($ord);					
 //        $this->db->order_by($key,$value);
	// }
	if(!empty($ord)){
		foreach ($ord as $key => $value) {
			$this->db->order_by($key,$value);
		}           
            
	}
        $query = $this->db->get($table,$limit);
         if($first==1)
                return $query->row();
	else
                return $query->result();
    }

    //where in data
    public function where1in($table='',$where='')
    { 
    	 ////print_r($where); 
    	 
    	 $this->db->where_in('question_id',$where);
    	 $query=$this->db->get($table);  
    	// echo $this->db->last_query(); exit();  	
       return $query->result();

    }
      public function where1in2($table='',$where='')
    { 
    	 ////print_r($where); 
    	 
    	 $this->db->where_in('module_id',$where);
    	 $query=$this->db->get($table);  
    	// echo $this->db->last_query(); exit();  	
       return $query->result();

    }
    
    public function countData($table="",$whr="")
    {
		if(!empty($whr))
		$this->db->where($whr);
				
		$query = $this->db->get($table);
		return $query->num_rows();
    }
	
	public function settings(){
		$array = $this->getData('settings',"","","",0);
		$new = array();
		foreach($array as $key){
			$new[$key->key1]=$key->value;
		}
		return $new;
	}

	public function InsertData($table="",$data="")
	{
		if(!empty($data))
		{ $this->db->insert($table, $data);  return $this->db->insert_id(); 
		}else return false;
	}

	public function UpdateData($table="",$data="",$whr="")
	{
		if(!empty($data) && !empty($whr))
		{ 
			$this->db->update($table, $data, $whr);
			return true; 
		}else return false;
	}
	
	public function DeleteData($table="",$data="")
	{
		if(!empty($data))
		{ 
			$this->db->delete($table, $data); 
			return true; 
		}else return false;
	}
	
	public function get_user_img($table,$id)
	{
			$this->db->where($id);
			$query=$this->db->get($table);
			return $query->result();

	}

	 

	 public function get_few_record($table='',$select='',$where='')
	 {
		 	$this->db->select($select); 
		    $this->db->from($table);   
		    $this->db->where($where);
		    return $this->db->get()->row();
	 }
	 public function get_count($table='',$select='',$where)
	 {
	 	  $this->db->select($select); 
		    $this->db->from($table);   
		    $this->db->where($where);
		    return $this->db->get()->row();
	 }

  public function get_few_record_not_in($table='',$where='',$where_not='')
  {
  		$this->db->select('*');
  		$this->db->from($table);  
  		$this->db->where($where); 		
		$this->db->where($where_not);
		return  $this->db->get()->result();

  }
  
    public function selectdata($table)
  {
  		$this->db->select('*');
  		$this->db->from($table);  
		return  $this->db->get()->result();
  }
   public function select_cond($table,$where)
	 {
		 	$this->db->select("*"); 
		    $this->db->from($table);   
		    $this->db->where($where);
		    return $this->db->get()->row();
	 }
	 public function select_($table,$where)
	 {
		 	$this->db->select("*"); 
		    $this->db->from($table);   
		    $this->db->where($where);
		    return $this->db->get()->result();
	 }
	public function update_data($table,$data,$cond){
		 $this->db->where($cond);
        return $this->db->update($table, $data);
		
	}

	public function project_role_join($id="",$busy="")
	{
		$this->db->select('*');
		$this->db->from('pro_users');
		$this->db->join('roles','pro_users.role_id=roles.role_id');
		   if($id!="")
		   {
		     $this->db->where('pro_users.user_id',$id);
		   }
		   if($busy!="")
		   {
		     $this->db->where('pro_users.busy',$busy);
		   }
		$this->db->order_by('pro_users.user_id','desc');
	    return $this->db->get()->result();
	}
	public function get_todo_notifications()
	{
		$this->db->select('*');
		$this->db->from('todo_done_by_emp');
		$this->db->join('todo','todo_done_by_emp.todo_id=todo.todo_id');
		$this->db->join('projects','projects.project_id=todo.project_id');
		$this->db->where('todo_done_by_emp.admin_seen_status',0);		   
		$this->db->order_by('todo_done_by_emp.id','desc');
	    return $this->db->get()->result();
	}
	public function get_role_emp_join($id='')
	{
		// $this->db->select('*');
		// $this->db->from('assign_by');
		// $this->db->join('roles','assign_by.role_id=roles.role_id');

		   // if($id!="")
		   // {
		   //   $this->db->where('assign_by.assign_by_id',$id);
		   // }
		// $this->db->order_by('assign_by.assign_by_id','desc');
		$this->db->select('*');
		$this->db->from('roles');
		if($id!="")
	    {
	     $this->db->where('role_id',$id);
	    }		
		$this->db->order_by('role_id','asc');
	    return $this->db->get()->result();
	}
	/*public function project_mile_join($id="")
	{
		$this->db->select('*');
		$this->db->from('projects');
		$this->db->join('project_miles','projects.project_id=project_miles.project_id');

		   if($id!="")
		   {
		     $this->db->where('pro_users.user_id',$id);
		   }
		$this->db->order_by('pro_users.user_id','desc');
	    return $this->db->get()->result();
	}*/


 public function get_total_projects_detail($id='',$history=false) 
  { 
  	//$this->db->select('id')->from('employees_backup');
   $subQuery =  $this->db->get_compiled_select();
  	  $this->db->select('projects.project_name,pro_users.user_name,manage_task.task_id,manage_task.description,manage_task.created_on,manage_task.createded_by, manage_task.status as project_status,task_report.status as work_status,task_report.end_date,task_report.created_on as task_start_date,task_report.status as task_status,task_report.task_report_id');
		$this->db->from('manage_task');
		$this->db->join('pro_users','manage_task.employee_id=pro_users.user_id');
		$this->db->join('projects','manage_task.project_id=projects.project_id');
		$this->db->join('task_report','manage_task.task_id=task_report.project_report_id');

		   if($id!="")
		   {
		     $this->db->where('manage_task.task_id',$id);
		     $this->db->order_by('task_report.status','asc');
		   }
		//$this->db->order_by('manage_task.task_id','desc');

		// $this->db->where('assign_by.assign_by_id',$id);
		 if($history==false)
		 {
		 $this->db->where(' task_report.task_report_id = (select max(task_report.task_report_id) from  task_report  WHERE task_report.project_report_id=manage_task.task_id)');
		 $this->db->order_by('task_report.end_date','asc');
		 }		
		// $this->db->order_by('manage_task.task_id','desc');
		// $this->db->order_by('task_report.status','asc');
		
	    return $this->db->get()->result();
  }


 public function get_total_projects_detail2($id,$history=false) 
  { 
  	//$this->db->select('id')->from('employees_backup');
   $subQuery =  $this->db->get_compiled_select();
  	  $this->db->select('projects.project_name,pro_users.user_name,manage_task.task_id,manage_task.description,manage_task.created_on,manage_task.createded_by, manage_task.status as project_status,task_report.status as work_status,task_report.end_date,task_report.created_on as task_start_date,task_report.status as task_status,task_report.task_report_id');
		$this->db->from('manage_task');
		$this->db->join('pro_users','manage_task.employee_id=pro_users.user_id');
		$this->db->join('projects','manage_task.project_id=projects.project_id');
		$this->db->join('task_report','manage_task.task_id=task_report.project_report_id');

		   
		     $this->db->where($id);
		     $this->db->order_by('task_report.status','asc');
		   
		//$this->db->order_by('manage_task.task_id','desc');

		// $this->db->where('assign_by.assign_by_id',$id);
		 if($history==false)
		 {
		 $this->db->where('task_report.task_report_id = (select max(task_report.task_report_id) from  task_report  WHERE task_report.project_report_id=manage_task.task_id)');
		 $this->db->order_by('task_report.end_date','asc');
		 }		
		// $this->db->order_by('manage_task.task_id','desc');
		// $this->db->order_by('task_report.status','asc');
		
	    return $this->db->get()->result();
  }
  
   public function get_total_projects_detail3($id,$history=false) 
  { 
     // echo '<pre>'; print_r($id); die;
  	//$this->db->select('id')->from('employees_backup');
   $subQuery =  $this->db->get_compiled_select();
  	  $this->db->select('projects.project_name,pro_users.user_name,manage_task.task_id,manage_task.description,manage_task.created_on,manage_task.createded_by, manage_task.status as project_status,task_report.status as work_status,task_report.end_date,task_report.created_on as task_start_date,task_report.status as task_status,task_report.task_report_id');
		$this->db->from('manage_task');
		$this->db->join('pro_users','manage_task.employee_id=pro_users.user_id', 'left');
		$this->db->join('projects','manage_task.project_id=projects.project_id', 'left');
		$this->db->join('task_report','manage_task.task_id=task_report.project_report_id', 'left');

		   
		    // $this->db->where($id);
		    if(isset($id['created_on']))
		    {
		      $this->db->where('date(manage_task.created_on) >=', date($id['created_on']));
		    }
		    if(isset($id['end_date']))
		    {
		      $this->db->where('date(task_report.end_date) <=', date($id['end_date']));
		    }
		    if(isset($id['project_id']))
		    {
		       $this->db->where('projects.project_id', $id['project_id']);
		    }
             
		     $this->db->order_by('task_report.status','asc');
		   
		//$this->db->order_by('manage_task.task_id','desc');

		// $this->db->where('assign_by.assign_by_id',$id);
		 if($history==false)
		 {
		 $this->db->where('task_report.task_report_id = (select max(task_report.task_report_id) from  task_report  WHERE task_report.project_report_id=manage_task.task_id)');
		 $this->db->order_by('task_report.end_date','asc');
		 }		
		// $this->db->order_by('manage_task.task_id','desc');
		// $this->db->order_by('task_report.status','asc');
		
	    return $this->db->get()->result();
  } 
	
  public function todo_join($id='')
  {
  	 
  	 $this->db->select('todo.*,projects.project_name,projects.client_name');
  	 $this->db->from('todo');
  	 $this->db->join('projects','todo.project_id=projects.project_id');
  	 $this->db->order_by('todo.todo_id','Desc');
  	 return $this->db->get()->result();

  }

   public function common_whre_in($table='',$column="",$where='')
    {     	 
    	 $this->db->where_in($column,$where);
    	 $query=$this->db->get($table);  
    // echo $this->db->last_query(); exit();  	
       return $query->result();

    }
  
}

?>