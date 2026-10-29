@if(session('status'))
    <div class="{{ $wrap ?? 'portal-flashes' }}"><div class="flash success" role="status">{{ session('status') }}</div></div>
@endif
@if($errors->any())
    <div class="{{ $wrap ?? 'portal-flashes' }}"><div class="flash error" role="alert">{{ $errors->first() }}</div></div>
@endif
