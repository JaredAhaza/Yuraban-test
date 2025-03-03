import 'dart:convert';
import 'package:http/http.dart' as http;
const String baseUrl = 'http://10.0.2.2:8000/api';
Future<Map<String, dynamic>> registerRequest(Map<String, String> requestBody) async {
  final response = await http.post(
    Uri.parse('$baseUrl/customer/register'),
    headers: {"Content-Type": "application/json"},
    body: jsonEncode(requestBody),
  );

  return jsonDecode(response.body);
}

Future<List<Map<String, dynamic>>> fetchCounties() async {
  final response = await http.get(Uri.parse('$baseUrl/counties'));

  if (response.statusCode == 200) {
    final data = json.decode(response.body);
    return List<Map<String, dynamic>>.from(data['data']);
  } else {
    throw Exception("Failed to load counties");
  }
}

Future<List<String>> fetchSubCounties(int countyId) async {
  final response = await http.get(Uri.parse('$baseUrl/sub-counties/$countyId'));

  if (response.statusCode == 200) {
    final data = json.decode(response.body);
    return List<String>.from(data['data'].map((subCounty) => subCounty['name']));
  } else {
    throw Exception("Failed to load sub-counties");
  }
}