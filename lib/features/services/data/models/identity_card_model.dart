class IdentityCardModel {
  final int? id;
  final String cardNumber;
  final String nationalId;
  final String holderName;
  final String status;
  final DateTime? issueDate;
  final DateTime? expiryDate;
  final String? photoUrl;

  final List<Map<String, dynamic>> birthCertificates;
  final List<Map<String, dynamic>> deathCertificates;
  final List<Map<String, dynamic>> passports;
  final List<Map<String, dynamic>> familyCards;
  final List<Map<String, dynamic>> serviceRequests;
  final List<Map<String, dynamic>> appointments;
  final List<Map<String, dynamic>> payments;

  const IdentityCardModel({
    this.id,
    required this.cardNumber,
    required this.nationalId,
    required this.holderName,
    required this.status,
    this.issueDate,
    this.expiryDate,
    this.photoUrl,
    this.birthCertificates = const [],
    this.deathCertificates = const [],
    this.passports = const [],
    this.familyCards = const [],
    this.serviceRequests = const [],
    this.appointments = const [],
    this.payments = const [],
  });

  factory IdentityCardModel.fromJson(Map<String, dynamic> json) {
    List<Map<String, dynamic>> list(String key) {
      final raw = json[key];
      if (raw is List) {
        return raw
            .whereType<Map>()
            .map((e) => Map<String, dynamic>.from(e))
            .toList();
      }
      return const [];
    }

    return IdentityCardModel(
      id: json['id'] is int ? json['id'] as int : null,
      cardNumber: json['card_number']?.toString() ?? '',
      nationalId: json['national_id']?.toString() ?? '',
      holderName: json['holder_name']?.toString() ?? '',
      status: json['status']?.toString() ?? 'active',
      issueDate: json['issue_date'] != null
          ? DateTime.tryParse(json['issue_date'].toString())
          : null,
      expiryDate: json['expiry_date'] != null
          ? DateTime.tryParse(json['expiry_date'].toString())
          : null,
      photoUrl: json['photo_url']?.toString(),
      birthCertificates: list('birth_certificates'),
      deathCertificates: list('death_certificates'),
      passports: list('passports'),
      familyCards: list('family_cards'),
      serviceRequests: list('service_requests'),
      appointments: list('appointments'),
      payments: list('payments'),
    );
  }

  Map<String, int> get serviceCounts => {
        'birth_certificates': birthCertificates.length,
        'death_certificates': deathCertificates.length,
        'passports': passports.length,
        'family_cards': familyCards.length,
        'service_requests': serviceRequests.length,
        'appointments': appointments.length,
        'payments': payments.length,
      };

  bool get isActive => status == 'active';

  String get statusAr {
    switch (status) {
      case 'active':
        return 'سارية';
      case 'expired':
        return 'منتهية';
      case 'lost':
        return 'مفقودة';
      case 'suspended':
        return 'موقوفة';
      default:
        return status;
    }
  }
}