import 'dart:convert';

import 'package:http/http.dart' as http;

Future<void> registerResquests() async {
  const baseUrl = 'http://10.0.2.2:8000/api/customer/register';
  Map<String,String> requestBody = {
    "first_name": "Test",
    "last_name": "four",
    "gender": "male",
    "email": "test93@gmail.com",
    "phone": "+254712345379",
    "role": "driver",
    "county_id": "23",
    "subcounty":"Loima",
    "password": "1234"
  };
  final requestResponse = await http.post(Uri.parse(baseUrl),
  headers: {"Content-Type":"application/json"},
  body: jsonEncode(requestBody));
  final Map<String,dynamic> responseJson = jsonDecode(requestResponse.body);
  if(requestResponse.statusCode == 201){
    String message = responseJson["message"];
    String userFirstName = responseJson["user"]["first_name"];
    print("Response:  $userFirstName");
  }else{
    String emailError= responseJson["message"]["email"][0];
    String phoneError= responseJson["message"]["phone"][0];
    print("error:  $emailError $phoneError");
  }
}