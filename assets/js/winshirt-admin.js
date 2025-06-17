(function(wp){
  const { createElement } = wp.element;
  const { render } = wp.element;

  function App(props){
    return createElement('div', null, 'Interface ' + props.page + ' en d\xC3\xA9veloppement');
  }

  document.addEventListener('DOMContentLoaded', function(){
    var root = document.getElementById('winshirt-admin-root');
    if(root){
      var page = root.getAttribute('data-page') || '';
      render(createElement(App, {page: page}), root);
    }
  });
})(window.wp || {element:{createElement:function(){},render:function(){}}});
