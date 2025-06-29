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

    var testBtn = document.getElementById('winshirt-test-ia-key');
    if(testBtn){
      testBtn.addEventListener('click', function(e){
        e.preventDefault();
        var field = document.getElementById('winshirt-ia-api-key');
        var key = field ? field.value : '';
        testBtn.disabled = true;
        testBtn.textContent = 'Test en cours...';
        window.fetch(ajaxurl, {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
          body: new URLSearchParams({
            action: 'winshirt_test_ia_key',
            key: key
          })
        }).then(function(r){return r.json();}).then(function(res){
          alert(res.data && res.data.message ? res.data.message : (res.success ? 'Succès' : 'Erreur'));
        }).catch(function(){
          alert('Erreur de requête');
        }).finally(function(){
          testBtn.disabled = false;
          testBtn.textContent = 'Tester la clé';
        });
      });
    }
  });
})(window.wp || {element:{createElement:function(){},render:function(){}}});
