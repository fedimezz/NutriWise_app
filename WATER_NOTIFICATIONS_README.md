# 💧 Enhanced Water Notification System

## How It Works

The NutriWise water notification system now provides **personalized, progressive reminders** to help users maintain proper hydration.

### 🎯 Key Features

#### **1. Personalized Water Goals**
- Users can set their own daily water target (default: 1.5L)
- Configurable in Notification Preferences (0.5L - 5.0L range)
- Each user gets reminders based on their personal goal

#### **2. Progressive Reminder Levels**

The system sends different types of notifications based on water intake percentage:

| Water Intake | Notification Type | Icon | Message Tone |
|-------------|------------------|------|--------------|
| < 50% of goal | **🚨 Urgent** | 🚨 | Critical health warning |
| 50-75% of goal | **⚠️ Moderate** | ⚠️ | Strong encouragement |
| 75-100% of goal | **💧 Light** | 💧 | Gentle reminder |

#### **3. No Water Logged Reminder**
- Separate notification for users who haven't logged any water intake
- Encourages users to start tracking their hydration

### 🔧 Technical Implementation

#### **Database Changes**
```sql
ALTER TABLE user_preferences
ADD COLUMN objectif_eau_journalier DECIMAL(3,1) DEFAULT 1.5;
```

#### **Notification Rules**
1. **eau_insuffisante_leger** - Light reminder (75-99% of goal)
2. **eau_insuffisante_modere** - Moderate reminder (50-74% of goal)
3. **eau_insuffisante_urgent** - Urgent reminder (< 50% of goal)
4. **aucun_suivi_eau** - No water logged today

#### **User Interface**
- Water goal setting in notification preferences
- Visual feedback with appropriate icons
- Personalized messages with user's name and specific amounts

### 📊 Example Scenarios

**User A: Goal = 2.0L**
- Drinks 0.5L → 🚨 "Very insufficient! Only 0.5L of 2.0L goal!"
- Drinks 1.2L → ⚠️ "Insufficient - 1.2L today, 0.8L remaining"
- Drinks 1.8L → 💧 "Almost there! 1.8L, only 0.2L to go"

**User B: Goal = 1.0L (smaller person)**
- Drinks 0.3L → 🚨 "Critical! Only 0.3L of 1.0L goal!"
- Drinks 0.6L → ⚠️ "Drink more - 0.6L today, 0.4L remaining"

### 🔔 Anti-Spam Protection

- **24-hour cooldown**: Same water reminder type won't repeat within 24 hours
- **User preferences**: Can disable water notifications entirely
- **Active users only**: Only active users receive reminders

### ⚙️ Configuration

Users can customize:
- Daily water goal (0.5L - 5.0L)
- Enable/disable water notifications
- Reminder timing (runs daily at 20:00 by default)

This system ensures users stay properly hydrated while respecting their individual needs and preferences!