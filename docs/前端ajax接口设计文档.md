# AJAX接口设计文档

### Admin/QuestionBank
1. 判断题库别名是否重复
	* 请求地址：Admin/QuestionBank/checkAlias
	* 请求类型：POST
	* 请求参数：
		```
		{
			id:(int) 		//题库id（新增题库此项为0）
			alias:(str) 	//待判断别名
		}
		```
	* 成功时返回：`success`(str)
	* 失败时返回：`error`(str)

2. 切换发布状态
	* 请求地址：Admin/QuestionBank/changePublish
	* 请求类型：POST
	* 请求参数：
		```
		{
			id:(int) 		//题库id
			publish:(int) 	//待设置的发布状态，取值为0或1
		}
		```
	* 成功时返回：`success`(str)
	* 失败时返回：`error`(str)