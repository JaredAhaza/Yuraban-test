import 'package:flutter/material.dart';
import 'package:customer/services_api.dart'; // Import API services file

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  // Controllers for text fields
  final TextEditingController firstNameController = TextEditingController();
  final TextEditingController lastNameController = TextEditingController();
  final TextEditingController emailController = TextEditingController();
  final TextEditingController phoneController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  final TextEditingController subCountyController = TextEditingController();

  String selectedGender = "male"; // Default gender
  String selectedRole = "customer"; // Default role

  bool isLoading = false;

  // County and Sub-County Data
  List<Map<String, dynamic>> counties = [];
  List<String> subCounties = [];
  int? selectedCountyId;

  @override
  void initState() {
    super.initState();
    loadCounties(); // Fetch counties when the screen loads
  }

  // Function to fetch counties
  Future<void> loadCounties() async {
    try {
      List<Map<String, dynamic>> fetchedCounties = await fetchCounties();
      setState(() {
        counties = fetchedCounties;
      });
    } catch (e) {
      print("Error fetching counties: $e");
    }
  }

  // Function to fetch sub-counties
  Future<void> loadSubCounties(int countyId) async {
    try {
      List<String> fetchedSubCounties = await fetchSubCounties(countyId);
      setState(() {
        subCounties = fetchedSubCounties;
      });
    } catch (e) {
      print("Error fetching sub-counties: $e");
    }
  }

  Future<void> registerUser() async {
    setState(() {
      isLoading = true;
    });

    Map<String, String> requestBody = {
      "first_name": firstNameController.text,
      "last_name": lastNameController.text,
      "gender": selectedGender,
      "email": emailController.text,
      "phone": phoneController.text,
      "role": selectedRole,
      "county_id": selectedCountyId != null ? selectedCountyId.toString() : "", // Handle null case
      "subcounty": subCountyController.text,
      "password": passwordController.text,
    };

    try {
      final response = await registerRequest(requestBody);

      if (response["status"] == "success") {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Registration Successful!")),
        );
        Navigator.pop(context);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Error: ${response["message"]}")),
        );
      }
    } catch (error) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Failed to register: $error")),
      );
    } finally {
      setState(() {
        isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Register")),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: SingleChildScrollView(
          child: Column(
            children: [
              TextField(controller: firstNameController, decoration: InputDecoration(labelText: "First Name")),
              TextField(controller: lastNameController, decoration: InputDecoration(labelText: "Last Name")),
              TextField(controller: emailController, decoration: InputDecoration(labelText: "Email")),
              TextField(controller: phoneController, decoration: InputDecoration(labelText: "Phone")),
              TextField(controller: passwordController, decoration: InputDecoration(labelText: "Password"), obscureText: true),

              // County Dropdown
              DropdownButtonFormField<int>(
                value: selectedCountyId,
                items: counties.map((county) {
                  return DropdownMenuItem<int>(
                    value: county["id"],
                    child: Text(county["name"]),
                  );
                }).toList(),
                onChanged: (newValue) async {
                  setState(() {
                    selectedCountyId = newValue;
                    subCounties = [];
                  });
                  await loadSubCounties(newValue!);
                },
                decoration: InputDecoration(labelText: "County"),
              ),

              // Sub-County Dropdown
              DropdownButtonFormField<String>(
                value: subCounties.isNotEmpty ? subCounties[0] : null,
                items: subCounties.map((subCounty) {
                  return DropdownMenuItem<String>(
                    value: subCounty,
                    child: Text(subCounty),
                  );
                }).toList(),
                onChanged: (newValue) {
                  setState(() {
                    subCountyController.text = newValue!;
                  });
                },
                decoration: InputDecoration(labelText: "Sub-County"),
              ),

              // Gender Selection Dropdown
              DropdownButtonFormField<String>(
                value: selectedGender,
                items: ["male", "female"].map((String value) {
                  return DropdownMenuItem<String>(
                    value: value,
                    child: Text(value),
                  );
                }).toList(),
                onChanged: (newValue) {
                  setState(() {
                    selectedGender = newValue!;
                  });
                },
                decoration: InputDecoration(labelText: "Gender"),
              ),

              // Role Selection Dropdown
              DropdownButtonFormField<String>(
                value: selectedRole,
                items: ["customer", "driver"].map((String value) {
                  return DropdownMenuItem<String>(
                    value: value,
                    child: Text(value),
                  );
                }).toList(),
                onChanged: (newValue) {
                  setState(() {
                    selectedRole = newValue!;
                  });
                },
                decoration: InputDecoration(labelText: "Role"),
              ),

              const SizedBox(height: 20),
              isLoading
                  ? CircularProgressIndicator()
                  : ElevatedButton(
                onPressed: registerUser,
                child: Text("Register"),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
