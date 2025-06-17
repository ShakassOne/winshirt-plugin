jQuery(function($){
    var state = JSON.parse(localStorage.getItem('winshirt_state')) || {front:'', back:'', text:'', side:'front'};

    function saveState(){
        localStorage.setItem('winshirt_state', JSON.stringify(state));
    }

    function loadState(){
        var frontDefault = $('#winshirt-modal').data('default-front') || '';
        var backDefault  = $('#winshirt-modal').data('default-back') || '';
        if(state.front){
            $('#winshirt-preview-front img').attr('src', state.front);
        } else if(frontDefault){
            $('#winshirt-preview-front img').attr('src', frontDefault);
        }
        if(state.back){
            $('#winshirt-preview-back img').attr('src', state.back);
        } else if(backDefault){
            $('#winshirt-preview-back img').attr('src', backDefault);
        }
        if(state.text){
            $('#winshirt-text-input').val(state.text);
            $('.winshirt-text').text(state.text);
        }
        switchSide(state.side || 'front');
    }

    function switchSide(side){
        state.side = side;
        $('#winshirt-preview-front, #winshirt-preview-back').hide();
        if(side === 'back'){
            $('#winshirt-preview-back').show();
        }else{
            $('#winshirt-preview-front').show();
        }
        saveState();
    }

    $(document).on('click', '#winshirt-open-modal', function(e){
        e.preventDefault();
        $('#winshirt-modal').addClass('open');
    });

    $(document).on('click', '.winshirt-close', function(){
        $('#winshirt-modal').removeClass('open');
    });

    $(document).on('click', '.winshirt-tab-links a', function(e){
        e.preventDefault();
        var target = $(this).attr('href');
        $('.winshirt-tab-links li').removeClass('active');
        $(this).parent().addClass('active');
        $('.winshirt-tab').removeClass('active');
        $(target).addClass('active');
    });

    $(document).on('click', '.winshirt-upload', function(){
        $(this).next('input[type=file]').trigger('click');
    });

    $(document).on('change', '.winshirt-upload-input', function(){
        var side = state.side;
        var input = this;
        if(!input.files.length) return;
        var reader = new FileReader();
        reader.onload = function(e){
            if(side === 'back'){
                $('#winshirt-preview-back img').attr('src', e.target.result);
                state.back = e.target.result;
            } else {
                $('#winshirt-preview-front img').attr('src', e.target.result);
                state.front = e.target.result;
            }
            saveState();
        };
        reader.readAsDataURL(input.files[0]);
    });

    $(document).on('input', '#winshirt-text-input', function(){
        state.text = $(this).val();
        $('.winshirt-text').text(state.text);
        saveState();
    });

    $(document).on('click', '#winshirt-front-btn', function(){
        switchSide('front');
    });
    $(document).on('click', '#winshirt-back-btn', function(){
        switchSide('back');
    });

    loadState();
});
