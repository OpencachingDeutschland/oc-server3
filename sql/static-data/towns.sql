-- Table towns
SET NAMES 'utf8';
TRUNCATE TABLE `towns`;
INSERT INTO `towns` (`country`, `name`, `trans_id`, `coord_lat`, `coord_long`, `maplist`) VALUES
('AT', 'Bregenz', '2369', '48.20833', '16.373064', '1'),
('AT', 'Eisenstadt', '2370', '47.845556', '16.518889', '1'),
('AT', 'Graz', '2371', '47.066667', '15.433333', '1'),
('AT', 'Innsbruck', '2372', '47.267222', '11.392778', '1'),
('AT', 'Klagenfurt', '2373', '46.617778', '14.305556', '1'),
('AT', 'Linz', '2374', '48.303056', '14.290556', '1'),
('AT', 'Salzburg', '2375', '47.8', '13.033333', '1'),
('AT', 'Sankt Pölten', '2376', '48.204722', '15.626667', '1'),
('AT', 'Wien', '2377', '48.20833', '16.373064', '1'),
('CH', 'Aarau', '2378', '47.394443', '8.045', '1'),
('CH', 'Appenzell', '2379', '47.330828', '9.408615', '1'),
('CH', 'Herisau', '2380', '47.383329', '9.266671', '1'),
('CH', 'Liestal', '2381', '47.484', '7.735', '1'),
('CH', 'Basel', '2382', '47.557421', '7.592573', '1'),
('CH', 'Bern', '2383', '46.951081', '7.438637', '1'),
('CH', 'Fribourg', '2384', '46.806113', '7.162775', '1'),
('CH', 'Genève', '2385', '46.2', '6.15', '1'),
('CH', 'Glarus', '2386', '47.03333', '9.066666', '1'),
('CH', 'Chur', '2387', '46.85', '9.53333', '1'),
('CH', 'Delémont', '2388', '47.365282', '7.347242', '1'),
('CH', 'Luzern', '2389', '47.05', '8.3', '1'),
('CH', 'Neuchâtel', '2390', '46.990281', '6.930567', '1'),
('CH', 'Lausanne', '2391', '46.516672', '6.633324', '1'),
('CH', 'Zürich', '2392', '47.379022', '8.541001', '1'),
('DE', 'Berlin', '2270', '52.5234051', '13.4113999', '1'),
('DE', 'Bremen', '2271', '53.079294', '8.816126', '1'),
('DE', 'Dresden', '2272', '51.0509912', '13.7336335', '1'),
('DE', 'Düsseldorf', '2273', '51.2249429', '6.7756524', '1'),
('DE', 'Erfurt', '2274', '50.9737346', '11.0223989', '1'),
('DE', 'Hamburg', '2275', '53.580684', '9.992638', '1'),
('DE', 'Hannover', '2276', '52.3720683', '9.7356861', '1'),
('DE', 'Kiel', '2277', '54.3191939', '10.1316016', '1'),
('DE', 'Mainz', '2278', '49.9951227', '8.2674264', '1'),
('DE', 'Magdeburg', '2279', '52.130956', '11.636701', '1'),
('DE', 'München', '2280', '48.1391265', '11.5801863', '1'),
('DE', 'Potsdam', '2281', '52.3802099', '13.0723571', '1'),
('DE', 'Saarbrücken', '2282', '49.239851', '6.991278', '1'),
('DE', 'Schwerin', '2283', '53.625743', '11.416893', '1'),
('DE', 'Stuttgart', '2284', '48.7761027', '9.18113708', '1'),
('DE', 'Wiesbaden', '2285', '50.08408', '8.2383918', '1'),
('DE', 'Frankfurt am Main', '2286', '50.1134931', '8.6810548', '1'),
('DE', 'Dortmund', '2287', '51.5078845', '7.4702625', '1'),
('DE', 'Essen', '2288', '51.4456505', '7.0102164', '1'),
('DE', 'Leipzig', '2289', '51.3417825', '12.3936349', '1'),
('DE', 'Nürnberg', '2290', '49.4442306', '11.0725565', '1'),
('IT', 'Bologna', '2291', '44.497', '11.343', '1'),
('IT', 'Bolzano', '2292', '46.502', '11.354', '1'),
('IT', 'Corsica', '2293', '42.142924', '9.11388', '1'),
('IT', 'Cosenza', '2294', '39.296', '16.249', '1'),
('IT', 'Genova', '2295', '44.41148', '8.966', '1'),
('IT', 'Milano', '2296', '45.468', '9.185', '1'),
('IT', 'Napoli', '2297', '40.8561697', '14.2723721', '1'),
('IT', 'Palermo', '2298', '37.8', '13.5', '1'),
('IT', 'Roma', '2299', '41.902', '12.48', '1'),
('IT', 'Sardegna', '2300', '40.091047', '9.066147', '1'),
('IT', 'Udine', '2301', '46.066', '13.228', '1'),
('ES', 'A Coruña', '2302', '43.370873', '-8.395835', '1'),
('ES', 'Albacete', '2303', '38.997652', '-1.86007', '1'),
('ES', 'Alicante', '2304', '38.345203', '-0.481006', '1'),
('ES', 'Almería', '2305', '36.840164', '-2.467922', '1'),
('ES', 'Ávila', '2306', '40.656422', '-4.700322', '1'),
('ES', 'Badajoz', '2307', '38.878597', '-6.970283', '1'),
('ES', 'Barcelona', '2308', '41.387917', '2.169919', '1'),
('ES', 'Bilbao', '2309', '43.256963', '-2.923441', '1'),
('ES', 'Burgos', '2310', '42.340875', '-3.699731', '1'),
('ES', 'Cáceres', '2311', '39.476179', '-6.37076', '1'),
('ES', 'Cádiz', '2312', '36.529688', '-6.292657', '1'),
('ES', 'Castellón', '2313', '39.986068', '-0.036024', '1'),
('ES', 'Ceuta', '2314', '35.888287', '-5.316195', '1'),
('ES', 'Ciudad Real', '2315', '38.986096', '-3.927263', '1'),
('ES', 'Córdoba', '2316', '37.884727', '-4.779152', '1'),
('ES', 'Cuenca', '2317', '40.071835', '-2.134005', '1'),
('ES', 'Gerona', '2318', '41.981796', '2.8237', '1'),
('ES', 'Granada', '2319', '37.176487', '-3.597929', '1'),
('ES', 'Guadalajara', '2320', '40.629816', '-3.166493', '1'),
('ES', 'Huesca', '2321', '42.140102', '-0.408898', '1'),
('ES', 'Huelva', '2322', '37.257101', '-6.949555', '1'),
('ES', 'Jaén', '2323', '37.765739', '-3.789518', '1'),
('ES', 'Las Palmas de Gran Canaria', '2324', '28.124823', '-15.430007', '1'),
('ES', 'León', '2325', '42.599876', '-5.571752', '1'),
('ES', 'Lérida', '2326', '41.614152', '0.625782', '1'),
('ES', 'Logroño', '2327', '42.465776', '-2.449995', '1'),
('ES', 'Lugo', '2328', '43.012087', '-7.555851', '1'),
('ES', 'Madrid', '2329', '40.416691', '-3.700345', '1'),
('ES', 'Málaga', '2330', '36.719646', '-4.420019', '1'),
('ES', 'Melilla', '2331', '35.292339', '-2.938794', '1'),
('ES', 'Murcia', '2332', '37.983445', '-1.12989', '1'),
('ES', 'Ourense', '2333', '42.340009', '-7.864641', '1'),
('ES', 'Oviedo', '2334', '43.360259', '-5.844758', '1'),
('ES', 'Palencia', '2335', '42.012458', '-4.531175', '1'),
('ES', 'Palma de Mallorca', '2336', '39.569506', '2.649966', '1'),
('ES', 'Pamplona', '2337', '42.817991', '-1.644215', '1'),
('ES', 'Pontevedra', '2338', '42.433619', '-8.648053', '1'),
('ES', 'Salamanca', '2339', '40.964972', '-5.663047', '1'),
('ES', 'San Sebastián', '2340', '43.320736', '-1.984421', '1'),
('ES', 'Santa Cruz de Tenerife', '2341', '28.46981', '-16.25485', '1'),
('ES', 'Santander', '2342', '43.46096', '-3.807934', '1'),
('ES', 'Segovia', '2343', '40.949427', '-4.119209', '1'),
('ES', 'Sevilla', '2344', '37.38264', '-5.996295', '1'),
('ES', 'Soria', '2345', '41.763598', '-2.464921', '1'),
('ES', 'Tarragona', '2346', '41.118663', '1.24533', '1'),
('ES', 'Teruel', '2347', '40.34411', '-1.10691', '1'),
('ES', 'Toledo', '2348', '39.856778', '-4.024476', '1'),
('ES', 'Valencia', '2349', '39.470239', '-0.376805', '1'),
('ES', 'Valladolid', '2350', '41.652947', '-4.728388', '1'),
('ES', 'Vitoria-Gasteiz', '2351', '42.846406', '-2.667893', '1'),
('ES', 'Zamora', '2352', '42.846406', '-2.667893', '1'),
('ES', 'Zaragoza', '2353', '41.656287', '-0.876538', '1'),
('FR', 'Paris', '2354', '48.85568', '2.351074', '1'),
('FR', 'Marseille', '2355', '43.296699', '5.370598', '1'),
('FR', 'Lyon', '2356', '45.76405', '4.835701', '1'),
('FR', 'Toulouse', '2357', '43.60451', '1.444359', '1'),
('FR', 'Nice', '2358', '43.695431', '7.266083', '1'),
('FR', 'Nantes', '2359', '47.216537', '-1.553707', '1'),
('FR', 'Strasbourg', '2360', '48.581831', '7.748623', '1'),
('FR', 'Montpellier', '2361', '43.61085', '3.876801', '1'),
('FR', 'Bordeaux', '2362', '44.834935', '-0.577126', '1'),
('FR', 'Lille', '2363', '50.628558', '3.057289', '1'),
('FR', 'Rennes', '2364', '48.113391', '-1.675587', '1'),
('FR', 'Reims', '2365', '49.258619', '4.030952', '1'),
('FR', 'Le Havre', '2366', '49.495894', '0.134411', '1'),
('FR', 'Saint-Étienne', '2367', '45.438936', '4.388351', '1'),
('FR', 'Toulon', '2368', '43.124417', '5.93102', '1');
