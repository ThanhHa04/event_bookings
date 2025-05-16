USE ticket_selling;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE events (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    date VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL
);


CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    seat_number VARCHAR(10) NOT NULL,
    status ENUM('available', 'booked') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE special_events (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    date VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL
);

CREATE TABLE featured_events (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    date VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL
);
 

CREATE TABLE music_events (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    date VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL
);

CREATE TABLE visit_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    date VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL
);


INSERT INTO events (name, date, price, image, location) VALUES
('Giải cờ vua Hà Nội mở rộng GO-VCHESS 2025: Vietnam Chess Championship', '08:00 | Ngày 19 Tháng 4 Năm 2025', 500000, '1.webp', 'Hy Maxpro Coffee, Nhà F4, Ngõ 112 Trung Kính, Yên Hoà, Cầu Giấy, Hà Nội'),
('Madame de Đà Lạt', '07:30 |Ngày 16 Tháng 12 Năm 2024', 700000, '2.webp', 'Madame De Dalat (Biệt điện Trần Lệ Xuân), Số 2 Đường Yết Kiêu, phường 5, Lâm Đồng'),
('Combo ưu đãi - Giải chạy VTV Phú Quốc Marathon 2025 - Đêm concert Giai Điệu Hoàng Hôn (Hà Nhi - Lân Nhã - Tăng Phúc) ', '00:00 | Ngày 06 Tháng 4 Năm 2025', 600000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-6cc0298de9d68a16c9e6827697ce937e.jpg', 'Thành phố Phú Quốc, tỉnh Kiên Giang, Kiên Giang');
('Liveshow Trung Quân x Bùi Anh Tuấn: "Đến lúc thay đổi rồi"','20:00 | Ngày 02 Tháng 5 Năm 2025', 600000,'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-32369ec59951a94740a44ba820c18440.jpg', 'Dốc Mộng Mơ, Sky Graden, Tổ dân phố 1, Vĩnh Phúc');

INSERT INTO special_events (name, date, price, image, location) VALUES
('CINÉ FUTURE HITS #6: DƯƠNG DOMIC','21:00, 28 tháng 03, 2025 - 04:00, 29 tháng 03, 2025', 450000, 'https://salt.tkbcdn.com/ts/ds/fa/39/b9/994dcff56e823f3e36bbb39c20687f9e.jpg', 'CINÉ Saigon 148 Cống Quỳnh, Phường Phạm Ngũ Lão, Quận 1, Thành Phố Hồ Chí Minh'),
('129BPM: ĐỘNG PHÁCH TÁCH KÉN', '20:00 - 21:30, 18 tháng 04, 2025', 300000, 'https://salt.tkbcdn.com/ts/ds/e1/5a/93/6f1e3f3f40eb1bcee23d4125f8e166ad.jpg', 'Trung Tâm Nghệ Thuật Âu Cơ - 8 P. Huỳnh Thúc Kháng, Thành Công, Ba Đình, Hà Nội 8 Huỳnh Thúc Kháng, Phường Thành Công, Quận Ba Đình, Thành Phố Hà Nội'),
('HYPE! FEST', '09:30 - 23:00, 12 tháng 04, 2025', 500000, 'https://salt.tkbcdn.com/ts/ds/6d/e4/dc/15cdab78ab0a1dbd36b0ea97719942b6.jpg', 'Sân Vận Động Cần Thơ Cần Thơ, Phường Cái Khế, Quận Ninh Kiều, Thành Phố Cần Thơ'),
('[CONCERT] CHỊ ĐẸP 12.04.2025', '19:00 - 23:00, 12 tháng 04, 2025', 800000, 'https://salt.tkbcdn.com/ts/ds/fb/0f/0c/e48bd2a83fd262ddfc5265a043ca2524.jpg', 'The Global City | Đỗ Xuân Hợp, Tp Thủ Đức, Phường An Phú, Quận 2, Thành Phố Hồ Chí Minh'),
('Giảm 30% [Mã: QUOCTHAO30] [SK Quốc Thảo] Kịch Ma 4D: Mặt Nạ Quỷ- Khuyến Mãi Suất...', '19:30 - 21:30, 29 tháng 03, 2025', 80000, 'https://salt.tkbcdn.com/ts/ds/7d/9a/4b/30f29dfbb80c65b714fd4089beac8a2e.jpg', 'Sân Khấu Quốc Thảo
70-72 Nguyễn Văn Trỗi, P.8, Q.Phú Nhuận , Phường 08, Quận Phú Nhuận, Thành Phố Hồ Chí Minh'),
('Nhà Hát Kịch IDECAF: Cái gì Vui Vẻ thì mình Ưu Tiên', '18:00 - 21:00, 23 tháng 03, 2025', 270000, 'https://salt.tkbcdn.com/ts/ds/2b/1a/a5/371b379ac0bdd94e091cfc20ae2ce99d.jpg', 'Nhà Hát Kịch IDECAF
Số 28 Lê Thánh Tôn, Phường Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh'),
('MY MUSES - NAMTANFILM 1ST FANMEETING IN VIETNAM', '14:00 - 17:00, 29 tháng 03, 2025', 2000000, 'https://salt.tkbcdn.com/ts/ds/21/5f/d7/92e9981cc46850451627316bfea4abd5.jpg', 'Nhà Hát Bến Thành
Số 6 Mạc Đĩnh Chi, Phường Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh'),
('NOOS CHILL NIGHT THE CONCERT', '16:30 - 21:00, 01 tháng 05, 2025', 850000, 'https://salt.tkbcdn.com/ts/ds/76/9b/d7/a8dff545a691b99731712b43da67556a.jpg', 'Sân khấu Quảng trường Lavender Đà Lạt
Tiểu khu 157 Khu du lịch Tuyền Lâm, Phường 4, Thành Phố Đà Lạt, Tỉnh Lâm Đồng'),
('[Nhà Hát THANH NIÊN] Hài kịch: Thanh Xà Bạch Xà ngoại truyện', '19:00 - 22:00, 29 tháng 03, 2025', 250000, 'https://salt.tkbcdn.com/ts/ds/72/00/b4/c3ee374b63d5baf3d0a27b18d13e99ce.jpg', 'Nhà Văn hoá Thanh niên Thành phố Hồ Chí Minh
4 Phạm Ngọc Thạch, Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh'),
('Swan Lake', '20:00 - 22:00, 11 tháng 04, 2025', 1000000, 'https://salt.tkbcdn.com/ts/ds/64/84/6a/e9adbb6f7b0826db84c8821538ceaf9a.png', 'Nhà hát Hồ Gươm
40 P. Hàng Bài, Phường Hàng Bài, Quận Hoàn Kiếm, Thành Phố Hà Nội'),
('GAI CONCERT IN HANOI', '19:30 - 23:00, 05 tháng 04, 2025', 850000, 'https://salt.tkbcdn.com/ts/ds/d5/91/b9/d6d51e853d48514ec2a263cf50925d23.jpg', 'Cung Điền Kinh Mỹ Đình
KĐT Mỹ Đình, Trần Hữu Dực, Quận Nam Từ Liêm, Thành Phố Hà Nội'),
('[MINISHOW] B.U.I STORIES - Trung Quân & Bùi Anh Tuấn', '20:00 - 22:00, 10 tháng 04, 2025', 700000, 'https://salt.tkbcdn.com/ts/ds/dc/a9/b1/bc86663d9ef3140e9d2393857b05a75c.jpg', 'Nhà hát Bến Thành
Lầu 1, Số 6 Mạc Đĩnh Chi, Phường Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh');

INSERT INTO featured_events (name, date, price, image, location) VALUES
('Live-concert GIAI ĐIỆU HOÀNG HÔN 2025: Hà Nhi - Lân Nhã - Tăng Phúc', '19:30 | Ngày 06 Tháng 4 Năm 2025', 4800000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-b3234c764be98c2b243747a4d9d51db9.jpg', 'Bãi Biển Phú Quốc - Quảng trường biển Grand World, Gành Dầu, đảo Phú Quốc, TP. Phú Quốc, Kiên Giang'),
('Chamber Music Concert "Mon Amour" - Đêm nhạc thính phòng tại Sài Gòn', '19:00 | Ngày 29 Tháng 3 Năm 2025', 350000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-0334ff8fd9b5b9f34d18db242fb2cae4.jpg', 'STEINGRAEBER Hall, 766/1 Sư Vạn Hạnh, Phường 12, Quận 10, Hồ Chí Minh'),
('Vé vào cửa triển lãm Nghệ thuật Ánh sáng Metashow', '00:00 | Ngày 01 Tháng 1 Năm 2025', 490000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-d7396e37eb825935099081865c19af14.jpg', 'L4-L10 Tầng 4, Thiso Mall Sala, 10 Mai Chí Thọ, P. Thủ Thiêm, Quận 2, Hồ Chí Minh'),
('Vé Cáp treo Hương Bình - Kết nối Tam giác tâm linh: Chùa Hương - Chùa Tiên - Chùa Tam Chúc', '00:00 | Ngày 08 Tháng 2 Năm 2025 ',280000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-739cdb4f29498f84c498e17b0a87e4fd.jpg', 'Nhà ga cáp treo đầu Hòa Bình, xã Phú Nghĩa, huyện Lạc Thủy, Hòa Bình'),
('I-Museum - Bảo tàng nghệ thuật đa giác quan đầu tiên tại Việt Nam', '10:00 | Ngày 01 Tháng 1 Năm 2025', 160000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-fb38e50fceca859f64e401316e8507e9.jpg', '5th Floor / Tràng Tiền Plaza, 24 Hai Bà Trưng, Hoàn Kiếm, Hà Nội'),
('Vườn Ánh Sáng Lumiere Đà Lạt - Không gian nghệ thuật ánh sáng giữa lòng Đà Lạt', '09h00 - 21h00 tất cả các ngày trong tuần', 150000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-5f172d5eb272b6ff5e2874fa98c120f9.jpg', '222B Mai Anh Đào, Phường 8, Đà Lạt, Lâm Đồng');


INSERT INTO music_events (name, date, price, image, location) VALUES
('Liveshow MYRA TRẦN tại Đà Lạt - Ngày 01/05/2025', '17:30 | Ngày 01 Tháng 5 Năm 2025', 1200000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-6d6bcb71b1dd765b411f745eaf10fc98.jpg', 'Lululola Coffee+, Đầu đèo Prenn, Số 32/2 Đường 3/4, Phường 3, TP. Đà Lạt, Lâm Đồng'),
('Chamber Music Concert "Mon Amour" - Đêm nhạc thính phòng tại Sài Gòn', '19:00 | Ngày 29 Tháng 3 Năm 2025', 350000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-0334ff8fd9b5b9f34d18db242fb2cae4.jpg', 'STEINGRAEBER Hall, 766/1 Sư Vạn Hạnh, Phường 12, Quận 10, Hồ Chí Minh'),
('Liveshow MAI TIẾN DŨNG - VƯƠNG ANH TÚ tại Đà Lạt - Ngày 29/3/2025', '17:30 | Ngày 29 Tháng 3 Năm 2025', 500000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-05d70ead35e0765e1429c24456b57f1b.jpg', 'Lululola Coffee+, Đầu đèo Prenn, Số 32/2 Đường 3/4, Phường 3, TP. Đà Lạt, Lâm Đồng'),
('Live-concert GIAI ĐIỆU HOÀNG HÔN 2025: Hà Nhi - Lân Nhã - Tăng Phúc', '19:30 | Ngày 06 Tháng 4 Năm 2025', 4800000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-b3234c764be98c2b243747a4d9d51db9.jpg', 'Bãi Biển Phú Quốc - Quảng trường biển Grand World, Gành Dầu, đảo Phú Quốc, TP. Phú Quốc, Kiên Giang'),
('Liveshow Trung Quân x Bùi Anh Tuấn: "Đến lúc thay đổi rồi"','20:00 | Ngày 02 Tháng 5 Năm 2025', 600000,'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-32369ec59951a94740a44ba820c18440.jpg', 'Dốc Mộng Mơ, Sky Graden, Tổ dân phố 1, Vĩnh Phúc'),
('Liveshow HOÀNG HẢI tại Đà Lạt - Ngày 19.04.2025', '17:30 | Ngày 19 Tháng 4 Năm 2025', 1200000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-4844a4074d299ea6720717c6378ff4bf.jpg', 'Lululola Coffee+, Đầu đèo Prenn, Số 32/2 Đường 3/4, Phường 3, TP. Đà Lạt, Lâm Đồng'),
('"CHÀO SHOW" tại Sài Gòn: tôn vinh âm nhạc Việt Nam qua 30 nhạc cụ dân tộc độc đáo.', '19:00', 1300000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-981bec17a253a5810266669090ed531e.jpg', 'Chào Show, số 6 Nguyễn Siêu, Phường Bến Nghé, Hồ Chí Minh'),
('Liveshow LÂN NHÃ tại Đà Lạt - Ngày 12.04.2025', '17:30 | Ngày 12 Tháng 4 Năm 2025', 1400000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-7b8c08838ef43479ed7e1905e866926a.jpg', 'Lululola Coffee+, Đầu đèo Prenn, Số 32/2 Đường 3/4, Phường 3, TP. Đà Lạt, Lâm Đồng');

INSERT INTO visit_events (name, date, price, image, location) VALUES
('Tour Tham Quan Hà Nội Trên Xe Buýt 2 Tầng Hop On Hop Off', '00:00 | Ngày 01 Tháng 1 Năm 2025', 200000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-c6ff8d14c1750bd40ab2f39faff63e0c.png', 'Hồ Hoàn Kiếm, 07 Đinh Tiên Hoàng, Quận Hoàn Kiếm, Hà Nội'),
('Tour tham quan Sài Gòn – Chợ Lớn – Khu Vực Người Hoa bằng xe bus 2 tầng | Hop On Hop Off Viet Nam', '00:00 | Ngày 01 Tháng 1 Năm 2025', 200000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-7e210731682bf2cb7b2697bca7d5a5ca.png', 'Chợ Bến Thành, Số 23 Phan Chu Trinh, Quận 1 , Hồ Chí Minh'),
('Vé vào cửa triển lãm Nghệ thuật Ánh sáng Metashow', '00:00 | Ngày 01 Tháng 1 Năm 2025', 490000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-d7396e37eb825935099081865c19af14.jpg', 'L4-L10 Tầng 4, Thiso Mall Sala, 10 Mai Chí Thọ, P. Thủ Thiêm, Quận 2, Hồ Chí Minh'),
('Vé xe bus 2 tầng than quan trung tâm TP. Hồ Chí Minh | Ho Chi Minh City Tour Hop On Hop Off', '00:00 | Ngày 01 Tháng 1 Năm 2025', 500000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-7a55848d20172bc5ccf5293e12b3a466.png', 'Bưu điện Trung tâm TP, Số 02 Công Xã Paris, Quận 1, Hồ Chí Minh'),
('Vé Cáp treo Hương Bình - Kết nối Tam giác tâm linh: Chùa Hương - Chùa Tiên - Chùa Tam Chúc', '00:00 | Ngày 08 Tháng 2 Năm 2025 ',280000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-739cdb4f29498f84c498e17b0a87e4fd.jpg', 'Nhà ga cáp treo đầu Hòa Bình, xã Phú Nghĩa, huyện Lạc Thủy, Hòa Bình'),
('Tour đêm Văn Miếu - Van Mieu Night Tour', '18:30', 100000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-550a1b04b5aed165f2c6ef65b12bdce0.jpg', 'Văn Miếu – Quốc Tử Giám, 58 P. Quốc Tử Giám, Văn Miếu, Hà Nội'),
('Vườn Ánh Sáng Lumiere Đà Lạt - Không gian nghệ thuật ánh sáng giữa lòng Đà Lạt', '09:00', 200000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-5f172d5eb272b6ff5e2874fa98c120f9.jpg', '222B Mai Anh Đào, Phường 8, Đà Lạt, Lâm Đồng'),
('I-Museum - Bảo tàng nghệ thuật đa giác quan đầu tiên tại Việt Nam', '10:00 | Ngày 01 Tháng 1 Năm 2025', 160000, 'https://ticketgo.vn/uploads/images/event-gallery/event_gallery-fb38e50fceca859f64e401316e8507e9.jpg', '5th Floor / Tràng Tiền Plaza, 24 Hai Bà Trưng, Hoàn Kiếm, Hà Nội');