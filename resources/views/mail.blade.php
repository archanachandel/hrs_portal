<!-- <!DOCTYPE html>
<html>
<head>
</head>
    <body>
        <table>
            <tr>
                <td>{{$name}}</td>
            </tr>
            <tr>
                <td> Mail from:{{$from}}</td>
            </tr>
            <tr>
                <td>Subject:{{$subject}}</td>
            </tr>
            <tr>
            <td>
                <p> Dear {{$name}}</p>
                <p> We want to inform that lead  assigned to you by <span>{{$sender->name}}</span>  , lead deatil  given below </p>
                <p>message:{{$lead_detail->message}}</p>
                <p>lead id:{{$lead_detail->id}} </p>
                <p> name:{{$lead_detail->name}} </p>
                <p> email:{{$lead_detail->email}} </p>
                <p>phone:{{$lead_detail->phone_number}}</p>
            </td>
            </tr>
            <tr>
                <td>{{$data}}</td>
            </tr>
        </table>
    </body>
</html> -->
<!DOCTYPE html>
<html>
​
<head>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            font-weight: 400;
            font-size: 15px;
            line-height: 1.5;
        }
    </style>
</head>
​
<body>
​
    <div style="background: #e1e4e6; padding:0 8px; width: 510px; margin: 0 auto; color: #363636; border-radius: 6px;">
        <table  style="padding: 6px 0;" align="center">
            <tr>
                <td style="text-align: center;">
                <img src="{{ asset('images/seasialogo.png') }}" alt="logo" />
                    
                </td>
            </tr>
        </table >
        <table width="500" align="center" style="background: #fff; padding:16px; border-radius: 6px; ">
            
            <tr>
                <td style="text-align: center;">
                    <h2 style="margin: 8px 0;">Hello {{$name}}</h2>
                </td>
            </tr>
            <tr>
                <td>
                    <p style="margin: 4px 0;"><b>{{$sender->name}}</b> assigned a new lead to you.</p>
                    <!-- <p style="margin: 4px 0; padding-top: 8px;"><b>Lead Id:</b> 12</p> -->
                    <p style="margin: 4px 0;  padding-top: 8px;"><b>Lead Name:</b> {{$lead_detail->name}}</p>
                    <p style="margin: 4px 0;"><b>Lead E-mail:</b>{{$lead_detail->email}}</p>
                    <p style="margin: 4px 0;"><b>Lead Conact no:</b>{{$lead_detail->phone_number}}</p>
                    <p style="margin: 4px 0 16px; padding-bottom: 8px;"><b>Lead Message:</b> {{$lead_detail->message}}
                        </p>
                    <p style="margin: 4px 0; font-weight: 600;">For more details please click the button below.</p>
                </td>
            </tr>
            <tr>
                <td>
                <a
                        style="margin: 2px 0; background-color: #AB0E0E; color: #fff; border-color: #AB0E0E; padding: 4px 8px; border-radius: 6px; font-size: 14px;"
                        href="https://stgn.appsndevs.com/metaland/single-lead/{{$lead_detail->id}}"
                        target="_blank">Click Here!</a>
                </td>
            </tr>
            <tr>
                <td>
                    <p style="margin: 4px 0 0;"><b>Note:</b> This is a system generated email.</p>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td valign="top" width="33.333%" style="text-align: center; padding-right: 10px;">
                    <p>&copy; 2022 Seasia. All Rights Reserved</p>
                </td>
            </tr>
        </table>
    </date_interval_format>
</body>
​
</html>