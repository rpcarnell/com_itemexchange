var clearboth = {clear: 'both', padding: '3px'};
class WishTrade extends React.Component 
{
  render() {
    //return <div>Add {this.props.itemVars.title} to Wish List | Trade {this.props.itemVars.title}</div>;
    var items = [];
    items[2] = "Add " + this.props.itemVars.title +" to Wish List";
    items[1] = "Trade Your Copy of " + this.props.itemVars.title + "";
   // items[0] = "Request " + this.props.itemVars.title;
    return <ItemActions items={items} itemTitle={this.props.itemVars.title} itemid={this.props.itemVars.itemid} />;
  }
}

function ItemActions(props) {  
  return (
    <div>
      {props.items.map(function(item, i) {
        return (
          <div><a href="javascript:void(0)" className="itemsother" onClick={handleItemClick.bind(this, i, props)} key={i}>{item}</a></div>
        );
      })}
    </div>
  );
}
/*
var handleItemClick = function(i, props) 
{ 
    alert(JSON.stringify(props.itemid) + " and i is " + i);
    var cicue = "dont go ";
    ReactDOM.render(< ItemResult msg={cicue} />, document.getElementById('wishtradeResult') );
}*/


var handleItemClick = function(i, props) {
     var itemexurl = window.itemexurl;
     var itemid = props.items[1];
     cronframe.jQuery.post(itemexurl+ "index.php?option=com_itemexchange&task=itemoptions", {itemTitle: props.itemTitle, optionindex: i, itemid: props.itemid},//date2send: values, prevar: prevarray, option: option, taskordering: ascdesc},
     function(data)  {  
         var dat2 = JSON.parse(data);
         dat2.title = props.itemTitle;
         data = JSON.stringify(dat2);
         var msg = dat2.msg;
         if (dat2.error == 0) { ReactDOM.render(<ClickHandler tradeVars={data}> {msg} </ClickHandler>, document.getElementById('wishtradeResult') ); }
         else { document.getElementById('wishtradeResult').innerHTML = "<span style='color: #a00;'>" + msg + "</span>"; }
       /*  var data2 = JSON.parse(data);
         if (data2.error == 1) {  return; }
         var msg = data2.msg;
         ReactDOM.render(<ClickHandler tradeVars={data}> {msg} </ClickHandler>, document.getElementById('wishtradeResult') );*/
    });
}

var ItemResult = function(props)
{
    return <div>finaly done {props.msg}</div>;
}
var ClickHandler = React.createClass({
    render: function() { //  alert(JSON.stringify(this.props));
    // This takes any props passed to CheckLink and copies them to <a>
    return  <div style={clearboth}>{this.props.children} <YesNoTrade tradeVars={this.props.tradeVars} /></div>;
  }
});
var YesNoTrade = React.createClass({
    render: function() 
    {  
        var sen = JSON.parse(this.props.tradeVars);
        if (sen.yesno == 0)
        {
            return <div></div>;
        }
        else return  <div id="yesnobutton"><div onClick={handleClick3.bind(this, 5, this.props.tradeVars)}>YES</div><div onClick={cancelClick.bind(this, this.props.tradeVars)}>NO</div></div>;
  }
});
var handleClick3 = function(i, props) 
{ 
	 var z = JSON.parse(props);
     var itemid = z.itemid;
     cronframe.jQuery.post(itemexurl+ "index.php?option=com_itemexchange&task=itemfinal", {props: props},//date2send: values, prevar: prevarray, option: option, taskordering: ascdesc},
     function(data)  {  console.log("#3:" +data);  
         var data2 = JSON.parse(data);
         var msg = data2.msg;
         if (data2.error == 1) { document.getElementById('wishtradeResult').innerHTML = "<span style='color: #a00;'>" + msg + "</span>"; }
         else  { document.getElementById('wishtradeResult').innerHTML = "<span style='color: #0a0;'>" + msg + "</span>"; }
    });
}
var cancelClick = function(props) {
     var z = JSON.parse(props);
     var itemid = z.itemid;
      ReactDOM.render(<div></div>, document.getElementById("wishtradeResult") );
    
}
//********************************************************************************************************

var items = {};
items.title = cronframe.jQuery('#wishTrade').data('moviename');
items.itemid = cronframe.jQuery('#wishTrade').data('itemid');

ReactDOM.render(<WishTrade itemVars={items} />, document.getElementById('wishTrade'));
