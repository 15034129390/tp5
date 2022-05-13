/**
 * 日期处理
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
 (function(obj){
 	obj={};
var now = new Date(); //当前日期 
var nowDayOfWeek = now.getDay()==0?7:now.getDay(); //今天本周的第几天 
var nowDay = now.getDate(); //当前日 
var nowMonth = now.getMonth(); //当前月 
var nowYear = now.getYear(); //当前年 
nowYear += (nowYear < 2000) ? 1900 : 0; // 

var lastMonthDate = new Date(); //上月日期 
lastMonthDate.setDate(1); 
lastMonthDate.setMonth(lastMonthDate.getMonth()-1); 
var lastYear = lastMonthDate.getYear(); 
lastYear += (lastYear < 2000) ? 1900 : 0; // 
var lastMonth = lastMonthDate.getMonth(); 

//格局化日期：yyyy-MM-dd 
function formatDate(date) { 
	var myyear = date.getFullYear(); 
	var mymonth = date.getMonth()+1; 
	var myweekday = date.getDate(); 

	if(mymonth < 10){ 
		mymonth = "0" + mymonth; 
	} 
	if(myweekday < 10){ 
		myweekday = "0" + myweekday; 
	} 
	return (myyear+"-"+mymonth + "-" + myweekday); 
} 

//获得某月的天数 
function getMonthDays(myMonth,nowYear){ 
	var monthStartDate = new Date(nowYear, myMonth, 1); 
	var monthEndDate = new Date(nowYear, myMonth + 1, 1); 
	var days = (monthEndDate - monthStartDate)/(1000 * 60 * 60 * 24); 
	return days; 
}


//获得本季度的开端月份 
function getQuarterStartMonth(){ 
	var quarterStartMonth = 0; 
	if(nowMonth<3){ 
		quarterStartMonth = 0; 
	} 
	if(2<nowMonth && nowMonth<6){ 
		quarterStartMonth = 3; 
	} 
	if(5<nowMonth && nowMonth<9){ 
		quarterStartMonth = 6; 
	} 
	if(nowMonth>8){ 
		quarterStartMonth = 9; 
	} 
	return quarterStartMonth; 
}
/**
 * 今年
 * @param  {[type]} func [description]
 * @return {[type]}      [description]
 */
 obj.getYear=function(func){
 	func(formatDate(new Date(nowYear, 0, 1)),formatDate(new Date(nowYear, 11, 31)));
 }
/**
 * 近七天
 * @param  {[type]} func [description]
 * @return {[type]}      [description]
 */
 obj.getLast7Days=function  (func) {
 	var mm=new Date();
 	mm.setDate(nowDay-6);
 	func(formatDate(mm),formatDate(new Date()));
 }

/**
 * 本周
 * @return {[type]} [description]
 */
 obj.getWeek=function(func){
 	var weekEndDate = new Date(nowYear, nowMonth, nowDay + (7 - nowDayOfWeek)); 
 	var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek+1); 
 	func(formatDate(weekStartDate),formatDate(weekEndDate));
 }
/**
 * 上周
 * @param  {[type]} func [description]
 * @return {[type]}      [description]
 */
 obj.getLastWeek=function  (func) {
 	var weekEndDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek); 
 	var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek-7); 
 	func(formatDate(weekStartDate),formatDate(weekEndDate));
 }
/**
 * 本月
 * @param  {[type]} func [description]
 * @return {[type]}      [description]
 */
 obj.getMonth=function(func) {
 	var monthEndDate = new Date(nowYear, nowMonth, getMonthDays(nowMonth,nowYear)); 
 	var monthStartDate = new Date(nowYear, nowMonth, 1); 
 	func(formatDate(monthStartDate),formatDate(monthEndDate));
 }
/**
 * 上月
 * @param  {[type]} func [description]
 * @return {[type]}      [description]
 */
 obj.getLastMonth=function(func) {
 	var lastMonthEndDate = new Date(lastYear, lastMonth, getMonthDays(lastMonth,lastYear)); 
 	var lastMonthStartDate = new Date(lastYear, lastMonth, 1); 
 	func(formatDate(lastMonthStartDate),formatDate(lastMonthEndDate));
 }
/**
 * 获取本季度日期
 * @param  {[type]} func [description]
 * @return {[type]}      [description]
 */
 obj.getQuarter=function(func) {
 	var quarterEndMonth = getQuarterStartMonth() + 2; 
 	var quarterEndMonthDate = new Date(nowYear, quarterEndMonth, getMonthDays(quarterEndMonth,nowYear)); 
 	var quarterStartDate = new Date(nowYear, getQuarterStartMonth(), 1);
 	return func(formatDate(quarterStartDate),formatDate(quarterEndMonthDate)) ; 
 }
/**
 * 获取今日日期
 * @param  {[type]} func [description]
 * @return {[type]}      [description]
 */
 obj.getToday=function(func) {
 	func(formatDate(new Date()),formatDate(new Date()));
 }
/**
 * 昨天
 * @param  {[type]} func [description]
 * @return {[type]}      [description]
 */
 obj.getYesterday=function (func) {
 	var mm=new Date();
 	mm.setDate(nowDay-1);
 	var _time=formatDate(mm);
 	func(_time,_time);
 }
 window.locData=obj;

obj.getTomorrow=function (func) {
	var mm=new Date();
 	mm.setDate(nowDay+1);
 	var _time=formatDate(mm);
 	func(_time,_time);
}
})(window.locData);