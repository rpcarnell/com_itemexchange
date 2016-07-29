var handleClick2 = function(i, props) {
     var itemexurl = window.itemexurl;
     var itemid = props.items[1];
     cronframe.jQuery.post(itemexurl+ "index.php?option=com_itemexchange&task=useroptions", {optionindex: i, userid: props.items[0], itemid: props.items[1]},//date2send: values, prevar: prevarray, option: option, taskordering: ascdesc},
     function(data)  {  alert("#2:" +data);
         var data2 = JSON.parse(data);
         if (data2.error == 1) { alert(data2.msg); return; }
         var msg = data2.msg;
         ReactDOM.render(<ClickHandler tradeVars={data}> {msg} </ClickHandler>, document.getElementById("wishtradeMSG_"+ itemid) );
    });
}
var handleClick3 = function(i, props) {
     var z = JSON.parse(props);
     var itemid = z.itemid;
     cronframe.jQuery.post(itemexurl+ "index.php?option=com_itemexchange&task=userfinal", {props: props},//date2send: values, prevar: prevarray, option: option, taskordering: ascdesc},
     function(data)  {  console.log("#3:" +data);  
         var data2 = JSON.parse(data);
         if (data2.error == 1) { alert(data2.msg); return; }
         var msg = data2.msg;
         // ReactDOM.render(<span>hell</span>, document.getElementById("yesnobutton") );
         
        ReactDOM.render(<div style={clearboth}>{msg}</div>, document.getElementById("wishtradeMSG_"+ itemid) );
    });
}
function ExchangeNeeds(props) {  
  return (
    <div>
      {props.items[2].map(function(item, i) {
        return (
          <div className="itemsother" onClick={handleClick2.bind(this, i, props)} key={i}>{item}</div>
        );
      })}
    </div>
  );
}
function renderToElements(elements) {  
  for (var i = 0; i < elements.length; i++) {
       var items = [];
       items[2] = ['I Want This', 'I Own This'];
       items[1] = elements[i].getAttribute('id').replace('itemThree_', '');
       var bla = '#' + elements[i].getAttribute('id');
       items[0] = jQuery(bla).data('userid');
       ReactDOM.render(<ExchangeNeeds items={items} />, elements[i]);
  }
}
renderToElements(document.getElementsByClassName("threeothers"));

var clearboth = {clear: 'both', padding: '3px'};
var CheckLink = React.createClass({
    
  render: function() { //alert(this.props.href);
    // This takes any props passed to CheckLink and copies them to <a>
    return  <p style={clearboth}><a {...this.props}>{'√ '}{this.props.children}</a></p>;
  }
});
var ClickHandler = React.createClass({
    render: function() { //alert(this.props.href);
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
        else return  <div id="yesnobutton"><div onClick={handleClick3.bind(this, 5, this.props.tradeVars)}>YES</div><div>NO</div></div>;
  }
});

