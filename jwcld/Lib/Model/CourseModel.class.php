<?php
	class CourseModel extends Model {
		protected $tableName = "brw_kbinfo_graduate";
		protected $fields = array(
			'XN','XQ_M','PYCC_M','开课单位','课程代码','课程名称','课程类别','课程性质','上课班号','第一任课教师工号','第一任课教师姓名','周次','节次','上课地点'
		);
	}

?>