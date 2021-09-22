@extends('emails.layout')
@section('title', 'Contact::'.Config::get('constants.site_name'))
@section('content')
<tr>
  <td class='movableContentContainer' valign='top' style="padding-top: 20px;">
    <div class='movableContent'>
      <table width="520" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
          <td align='left'>
            <div class="contentEditableContainer contentTextEditable">
              <div class="contentEditable" align='center'>
                <h2>{{ trans('email.thanks_for_sub_full') }}!</h2>
              </div>
            </div>
          </td>
        </tr>
        <tr><td height='15'> </td></tr>
        <tr>
          <td align='left'>
            <div class="contentEditableContainer contentTextEditable">
              <div class="contentEditable" align='center'>                
              </div>
            </div>
          </td>
        </tr>
        <tr><td height='20'></td></tr>
        <tr>
         <td>
          <p style="color:#000;font-size:16px;font-weight:bold;margin-bottom:6px;">{{ trans('email.hi') }} @if(!empty($data->first_name)){{ucfirst($data->first_name).' '.ucfirst($data->last_name)}}@endif,</p>
          <p style="color:#000;font-size:16px;">{{ trans('email.thanks_for_sub_full') }}!</p>
         </td>
        </tr>
        <tr><td height='20'></td></tr>
      </table>
    </div>        
         
  </td>
</tr>
@endsection