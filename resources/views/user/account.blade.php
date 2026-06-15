@include('partials/header')

<div style='margin-bottom:10px;' class='form-container'>

    <h2>Update Account</h2>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <form id='accountForm'>

        <div class="form-group">
            <label>Name</label>
            <div class="input-group">
                <input type="text" name="name" value="{{ $user->name }}">
                <div class="field-error" id="name-error"></div>
            </div>
        </div>

        <div class="form-group">
            <label>Email</label>
            <div class="input-group">
                <input type="email" name="email" value="{{ $user->email }}">
                <div class="field-error" id="email-error"></div>
            </div>
        </div>

        <div class="form-group">
            <label>Password</label>
            <div class="input-group">
                <input type="password" name="password" value="">
                <div class="field-error" id="password-error"></div>
            </div>
        </div>

        <div class="form-group">
            <label>Repeat Password</label>
            <div class="input-group">
                <input type="password" name="password_confirmation" value="">
                <div class="field-error" id="password_confirmation-error"></div>
            </div>
        </div>

        <button type="submit" id="submitBtn">
            Submit
        </button>

    </form>

    <div id="message">df</div>

</div>
<script>
$('#accountForm').on('submit', function (e) {
    e.preventDefault();
    $('.field-error')
        .hide()
        .html('');
    let button = $('#submitBtn');
    let message = $('#message');
    message.hide().removeClass('success error');
    let name = $('input[name="name"]').val().trim();
    let email = $('input[name="email"]').val().trim();
    let password = $('input[name="password"]').val();
    let passwordConfirmation = $('input[name="password_confirmation"]').val();
    let hasErrors = false;
    if (name === '') {
        $('#name-error')
            .text('Name is required.')
            .show();
        hasErrors = true;
    }
    if (email === '') {
        $('#email-error')
            .text('Email is required.')
            .show();
        hasErrors = true;
    }
    if (password !== '' || passwordConfirmation !== '') {
        if (password === '') {
            $('#password-error')
                .text('Password is required.')
                .show();
            hasErrors = true;
        }
        if (passwordConfirmation === '') {
            $('#password_confirmation-error')
                .text('Please repeat your password.')
                .show();
            hasErrors = true;
        }
        if (password !== '' &&
            passwordConfirmation !== '' &&
            password !== passwordConfirmation) {
            $('#password_confirmation-error')
                .text('Passwords do not match.')
                .show();
            hasErrors = true;
        }
    }
    if (hasErrors) {
        return;
    }
    button.prop('disabled', true);
    $.ajax({
        url: "{{ route('account.update') }}",
        type: "POST",
        data: $(this).serialize() + '&_method=POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            message
                .removeClass('error')
                .addClass('success')
                .text(response.message || 'Record updated successfully.')
                .show();
        },
        error: function (xhr) {
            $('.field-error')
                .hide()
                .html('');
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function (field, messages) {
                    $('#' + field + '-error')
                        .html(messages.join('<br>'))
                        .show();
                });
            } else {
                $('#message')
                    .removeClass('success')
                    .addClass('error')
                    .text('Something went wrong !!!')
                    .show();
            }
        },
        complete: function () {
            button.prop('disabled', false);
        }
    });

});
</script>

@include('partials/footer')