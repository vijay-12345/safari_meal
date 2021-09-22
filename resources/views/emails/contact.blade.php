@extends('emails.layout')
@section('title', 'Contact::Food Fox')
@section('content')
<tr>
  <td class='movableContentContainer' valign='top' style="padding-top: 20px;">
    <div class='movableContent'>
      <table width="520" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
          <td align='left'>
            <div class="contentEditableContainer contentTextEditable">
              <div class="contentEditable">
                <h2>Contacted Person Detail</h2>
              </div>
            </div>
          </td>
        </tr>
        <tr align="left">
          <td style="padding:15px 0;border-bottom:1px solid #e3e3e3;">First Name:</td>
          <td style="padding:15px 0;border-bottom:1px solid #e3e3e3;">{{$data['fname']}}</td>
        </tr>
        <tr align="left">
          <td style="padding:15px 0;border-bottom:1px solid #e3e3e3;">Last Name:</td>
          <td style="padding:15px 0;border-bottom:1px solid #e3e3e3;">{{$data['lname']}}</td>
        </tr>
        <tr align="left">
          <td style="padding:15px 0;border-bottom:1px solid #e3e3e3;">Email:</td>
          <td style="padding:15px 0;border-bottom:1px solid #e3e3e3;">{{$data['email']}}</td>
        </tr> 
         <tr align="left">
          <td style="padding:15px 0;border-bottom:1px solid #e3e3e3;">Phone:</td>
          <td style="padding:15px 0;border-bottom:1px solid #e3e3e3;">{{$data['phone']}}</td>
        </tr> 
        <tr align="left">
          <td style="padding:15px 0;">Message:</td>
          <td style="padding:15px 0;">{{$data['message']}}</td>
        </tr>
        <tr><td height='15'> </td></tr>                       
      </table>
    </div>
  </td>
</tr>
@endsection