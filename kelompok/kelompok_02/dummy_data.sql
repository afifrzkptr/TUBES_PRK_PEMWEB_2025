-- Dummy Medical Records
INSERT INTO appointments (id_patient, id_doctor, date, time, status, queue_number) VALUES
(3, 2, CURDATE() - INTERVAL 5 DAY, '10:00:00', 'Done', 1),
(3, 2, CURDATE() - INTERVAL 30 DAY, '14:00:00', 'Done', 5);

INSERT INTO medical_records (id_appointment, diagnosis, treatment, notes) 
SELECT id_appointment, 'Flu Berat', 'Paracetamol 500mg (3x1)\nVitamin C\nIstirahat', 'Pasien disarankan banyak minum air putih.'
FROM appointments WHERE status = 'Done' AND date = CURDATE() - INTERVAL 5 DAY LIMIT 1;

INSERT INTO medical_records (id_appointment, diagnosis, treatment, notes) 
SELECT id_appointment, 'Radang Tenggorokan', 'Amoxicillin 500mg(3x1)\nIbuprofen 400mg', 'Habiskan antibiotik.'
FROM appointments WHERE status = 'Done' AND date = CURDATE() - INTERVAL 30 DAY LIMIT 1;
