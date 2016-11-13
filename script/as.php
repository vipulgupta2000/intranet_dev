function validateForm1()
{
var x=document.forms["leaveRequest"]["type"].value;
if (x==null || x=="")
  {
  
  alert("Select Type Cannot be Blank");
  return false;
  }
  
var x=document.forms["leaveRequest"]["txt1"].value;
if (x==null || x=="")
  {
  //errmsg=errmsg+"\nFrom Date cannot be blank";
  alert("From Date cannot be blank");
 return false;
  }

var x=document.forms["leaveRequest"]["txt2"].value;
if (x==null || x=="")
  {
 alert("To Date cannot be blank");
  return false;
  }

var x=document.forms["leaveRequest"]["ndays"].value;
if (x==null || x==""||isNaN(x))
  {
  alert("Please click on No. Of Days");
  return false;
  }

var x=document.forms["leaveRequest"]["reason"].value;
if (x==null || x=="")
  {
  alert("Please Provide the Reason");
  return false;
  }

}