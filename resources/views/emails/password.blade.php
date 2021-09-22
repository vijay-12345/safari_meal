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
                        <h2>{{ trans('email.hi') }} {{$user['first_name'].' '.$user['last_name']}},</h2>
                     </div>
                  </div>
               </td>
            </tr>
            <tr>
               <td height='15'> </td>
            </tr>
            <tr>
               <td align='left'>
                  <div class="contentEditableContainer contentTextEditable">
                     <div class="contentEditable" align='center'>
                        <p  style='text-align:left;color:#999999;font-size:14px;font-weight:normal;line-height:19px;'>
                           {{ trans('email.received_message') }}
                           <br>
                           <br>
                           <a href="{{ url('password/reset/'.$user['token']) }}" title="Reset Password">{{ trans('email.reset_password') }}</a>
                           <br>
                           <br>
                           {{ trans('email.or') }}
                           <br>
                           <br>
                           {{ trans('email.copy_link') }}
                           <br>
                           <a href="{{ url('password/reset/'.$user['token']) }}">{{ url('password/reset/'.$user['token']) }}</a>
                        </p>
                     </div>
                  </div>
               </td>
            </tr>
            <tr>
               <td height='20'></td>
            </tr>
         </table>
      </div>
   </td>
</tr>
@endsection