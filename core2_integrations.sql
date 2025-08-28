-- CORE II Integration Views for Core 1 and Core 3
-- Safe to run multiple times

-- Core 1 (Freight Operations & Shipment Execution) reference data
CREATE OR REPLACE VIEW v_core1_providers_active AS
SELECT 
  id AS provider_id,
  name AS provider_name,
  type AS provider_type,
  service_area,
  monthly_rate,
  status,
  contract_start,
  contract_end,
  updated_at
FROM providers
WHERE status = 'Active';

CREATE OR REPLACE VIEW v_core1_routes_active AS
SELECT 
  id AS route_id,
  name AS route_name,
  type AS route_type,
  start_point,
  end_point,
  distance,
  frequency,
  status,
  estimated_time,
  updated_at
FROM routes
WHERE status = 'Active';

CREATE OR REPLACE VIEW v_core1_service_points_active AS
SELECT 
  id AS service_point_id,
  name AS service_point_name,
  type AS service_point_type,
  location,
  services,
  status,
  updated_at
FROM service_points
WHERE status = 'Active';

-- Current/active tariffs effective today
CREATE OR REPLACE VIEW v_core1_tariffs_current AS
SELECT 
  id AS tariff_id,
  name AS tariff_name,
  category,
  base_rate,
  per_km_rate,
  per_hour_rate,
  priority_multiplier,
  status,
  effective_date,
  expiry_date,
  updated_at
FROM tariffs
WHERE status = 'Active'
  AND CURRENT_DATE() BETWEEN effective_date AND expiry_date;

-- Schedules that are active and effective today
CREATE OR REPLACE VIEW v_core1_schedules_current AS
SELECT 
  id AS schedule_id,
  name AS schedule_name,
  route,
  vehicle_type,
  departure,
  arrival,
  frequency,
  status,
  start_date,
  end_date,
  capacity,
  updated_at
FROM schedules
WHERE status = 'Active'
  AND CURRENT_DATE() BETWEEN start_date AND end_date;

-- Core 3 (Customer Relationship & Business Control) analytics/compliance
CREATE OR REPLACE VIEW v_core3_contracts AS
SELECT 
  id AS provider_id,
  name AS provider_name,
  monthly_rate,
  status,
  contract_start,
  contract_end,
  DATEDIFF(contract_end, CURRENT_DATE()) AS days_remaining,
  updated_at
FROM providers;

CREATE OR REPLACE VIEW v_core3_sop_compliance AS
SELECT 
  id AS sop_id,
  title,
  category,
  department,
  version,
  status,
  review_date,
  CASE WHEN review_date <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END AS due_within_30_days,
  updated_at
FROM sops;

CREATE OR REPLACE VIEW v_core3_operational_summary AS
SELECT 
  (SELECT COUNT(*) FROM providers) AS total_providers,
  (SELECT COUNT(*) FROM providers WHERE status='Active') AS active_providers,
  (SELECT IFNULL(SUM(monthly_rate),0) FROM providers WHERE status='Active') AS monthly_provider_spend,
  (SELECT COUNT(*) FROM routes) AS total_routes,
  (SELECT COUNT(*) FROM service_points) AS total_service_points,
  (SELECT COUNT(*) FROM tariffs WHERE status='Active' AND CURRENT_DATE() BETWEEN effective_date AND expiry_date) AS active_tariffs_current,
  (SELECT COUNT(*) FROM sops WHERE status='Active') AS active_sops,
  (SELECT COUNT(*) FROM schedules WHERE status='Active' AND CURRENT_DATE() BETWEEN start_date AND end_date) AS active_schedules_current;


