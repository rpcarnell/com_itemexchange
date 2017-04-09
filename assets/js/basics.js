var handleClick = function(i, props) {
  
  //index.php?option=com_community&view=profile&userid=734&Itemid=124
  cronframe.jQuery.post(window.itemexurl+ "index.php?option=com_itemexchange&task=userprofile", {userid: props.items[i][1] },//date2send: values, prevar: prevarray, option: option, taskordering: ascdesc},
    function(data)  {  window.location = data; });
    
}

function Traders(props) {    
    var imagestyle={margin: '10px', float: 'left', width: '80px'}; 
    var clearboth = {clear: 'both'};
    var padd = {padding: '5px'}
    console.log(props.items.length + " length is here");
    var liLeft = {float: 'left', 'margin-right' : '10px', 'width' : '350px', 'min-height' : '140px'};
    
     
    if (props.items.length) {
  return (
   <div>      
    <ul className="submisList">
      {props.items.map(function(item, i) {  
        return (
                   
          <li style={liLeft} key={i}>  
          {item[2] ? <img src={itemexurl + item[2]} style={imagestyle} /> : <img src={itemexurl + 'images/com_itemexchange/anonymous.png'} style={imagestyle} /> }
          <p>Requested by {item[0]}</p> 
           <p>Visit <a href={item[3]}>{item[0]}'s</a> page</p>
          {item[4] ? <div><a href={item[5]}>You have requested from {item[0]}.</a></div> : <div>You have not requested anything from {item[0]}.</div> }
           <div style={clearboth}></div>
          </li> );
      })}
    </ul><div style={clearboth} /><br /></div>
  );
    }
    else return (<div style={padd}>There are no requests</div>);
}

function getItemsByID(j)
{   
	
    var id = String(j.items).replace('itemidClass_', '');
    document.getElementById('itemid_' + id).innerHTML = "<img style='margin-left: 50px; height: 50px;' src='"+itemexurl+"images/com_itemexchange/loading.gif' />";
    cronframe.jQuery.post(window.itemexurl+ "index.php?option=com_itemexchange&task=itemrequests", {userid: window.traderID, itemid: id},//date2send: values, prevar: prevarray, option: option, taskordering: ascdesc},
    function(data)  { 
       data = JSON.parse(data);
       var arrayLength = data.length;
       var data_2 = [];
       
       for (var i = 0; i < arrayLength; i++) 
       { 
           var arrayD = [];
           arrayD[0] = data[i].username; 
           arrayD[1] = data[i].buyer; 
           arrayD[2] = data[i].thumb; 
           arrayD[3] = data[i].userurl; 
           arrayD[4] = data[i].requested; 
           arrayD[5] = data[i].requestedURL; 
           data_2[i] = arrayD;
            
        }
        ReactDOM.render(<Traders items={data_2} />, document.getElementById('itemid_' + id));
        jQuery('#itemid_' + id).slideDown();
    });
    
}
function Nav(props)
{  
     var c = props.items[1];
     props.items = props.items[0];//the other one will use only one value
     return (<a onClick={getItemsByID.bind(this, props)} href='javascript:void(0)' class='requests'>Number of requests: {c}</a>);
}
function renderToElements(elements) {
  for (var i = 0; i < elements.length; i++) {
       var items = [];
       items[0] = cronframe.jQuery(elements[i]).attr('id');
       items[1] = cronframe.jQuery('#itemid_' + items[0].replace('itemidClass_', '')).data('requests');
       var cindy = 5;
       ReactDOM.render(<Nav items={items} />, elements[i]);
  }
}
renderToElements(document.getElementsByClassName("aitemrequests"));

 
