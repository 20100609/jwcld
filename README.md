# jwcld
Thinkphp framework website

北师大教务处领导干部听课平台项目
Thinkphp 3.1.3 version support

1. 访问路径：http://localhost:端口号/jwcld/index.php/Index/index
2. 删除 \jwcld\jwcld\Runtime 下除default.html文件以外的所有文件【缓存文档，会影响修改后页面效果】
3. 数据库文件jwcld_db.sql导入mysql数据库

2017.4.6任务
新增视图：ld_hz_leaderdetailcount
先到数据库里把实验室服务器上的jwcld数据库备份，备份后在本地还原，我已将此视图放到表上，做一下“各单位领导听课评价”表的导出