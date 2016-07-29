var handleClick = function(i, props) {
  
  //index.php?option=com_community&view=profile&userid=734&Itemid=124
  cronframe.jQuery.post(window.itemexurl+ "index.php?option=com_itemexchange&task=userprofile", {userid: props.items[i][1] },//date2send: values, prevar: prevarray, option: option, taskordering: ascdesc},
    function(data)  {  window.location = data; });
    
}

function Traders(props) {    
    var imagestyle={margin: '10px', float: 'left'}; 
    var clearboth = {clear: 'both'};
    var padd = {padding: '5px'}
    console.log(props.items.length + " length is here");
    
    if (props.items.length) {
  return (
   <div>      
    <ul className="submisList">
      {props.items.map(function(item, i) { //alert(item[0]);
        return (
                   
          <li onClick={handleClick.bind(this, i, props)} key={i}> 
          {item[2] ? <img src={itemexurl + item[2]} style={imagestyle} /> : '' }<p>Requested by {item[0]}</p> 
  <div style={clearboth}></div>
          </li> );
      })}
    </ul><br /></div>
  );
    }
    else return (<div style={padd}>There are no items</div>);
}
function getItemsByID(j)
{
    var id = String(j.items).replace('itemidClass_', '');
    // alert(id);
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
           //alert(JSON.stringify(data[i]) );
           data_2[i] = arrayD;
          // alert(i);
       }
        // alert(JSON.stringify(data_2) );
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

 