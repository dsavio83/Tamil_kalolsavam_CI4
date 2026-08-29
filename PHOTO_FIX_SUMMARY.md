# Photo Display and Preview Fix - Summary

## Problem Statement
The school_entry page had issues with:
1. Participant list photos not displaying properly
2. Edit modal photo preview not showing correctly
3. Photo not updating when changed (same filename replacement issue)
4. Participant list not sorted by item code in ascending order

## Solution Implemented

### 1. Photo Storage System Understanding
- **File Naming Convention**: `{school_code}_{admission_number}.{extension}`
- **Storage Path**: `photos/{sub_district_code}/{school_code}/`
- **Supported Extensions**: jpg, jpeg, gif, png
- **File Replacement**: When uploading a new photo with same admission number, the old file is automatically replaced due to `overwrite => TRUE` in upload config

### 2. Changes Made

#### A. Controller (Registration.php)
**Location**: `application/controllers/schools/Registration.php`

**Added Helper Method**:
```php
private function loadParticipantPhotos($participants, $schoolCode)
```
- Loads photos for all participants with proper cache busting
- Adds unique cache busters for list view: `&list={random}&ts={timestamp}`
- Ensures photos are refreshed on page load

**Enhanced get_participant_data Method**:
- Added enhanced cache busting for edit modal: `&edit={timestamp}&modal={random}`
- Ensures different cache keys for list vs modal views
- Returns proper photo URL with null checks

#### B. Model (Registration_Model.php)
**Location**: `application/models/schools/Registration_Model.php`

**Updated get_participant_details Method**:
```php
// Added JOIN with participant_item_details
$this->db->join('participant_item_details AS PID', 'PD.participant_id = PID.participant_id', 'LEFT');
$this->db->group_by('PD.participant_id');

// Added ORDER BY for item code ascending
$this->db->order_by('min_item_code', 'ASC');
$this->db->order_by('PD.class', 'ASC');
$this->db->order_by('PD.participant_name', 'ASC');
```
- Participants now sorted by their minimum item code (ascending)
- Secondary sort by class and name

#### C. View (school_entry.php)
**Location**: `application/views/schools/school_entry.php`

**JavaScript Functions Enhanced**:

1. **loadModalPhoto()** - Enhanced with better cache busting:
```javascript
var cacheBuster = Date.now() + '_' + Math.random();
var separator = photoUrl.indexOf('?') !== -1 ? '&' : '?';
currentPhoto.src = photoUrl + separator + 'v=' + cacheBuster;
```

2. **refreshModalPhoto()** - New function to refresh photo in modal:
```javascript
function refreshModalPhoto() {
    var currentPhoto = document.getElementById('currentPhoto');
    if (currentPhoto && currentPhoto.src && currentPhoto.src !== '') {
        var baseUrl = currentPhoto.src.split('?')[0];
        var cacheBuster = Date.now() + '_' + Math.random();
        currentPhoto.src = baseUrl + '?v=' + cacheBuster;
    }
}
```

3. **ensurePhotosLoad()** - New function to ensure list photos load:
```javascript
function ensurePhotosLoad() {
    var photos = document.querySelectorAll('.participant-photo');
    photos.forEach(function(photo) {
        if (photo.src && photo.src !== '') {
            var baseUrl = photo.src.split('?')[0];
            var cacheBuster = Date.now() + '_' + Math.random();
            photo.src = baseUrl + '?v=' + cacheBuster;
        }
    });
}
```

4. **editParticipant()** - Enhanced to:
   - Load photo with proper null checks
   - Call `removeCurrentPhoto()` if no photo exists
   - Open modal automatically after loading data

**HTML Changes**:
- Simplified photo URL generation in participant list
- Removed redundant cache busters from img tag (handled by controller)
- Added `loading="lazy"` attribute for better performance

### 3. How Photo Replacement Works

When a user uploads a new photo for an existing participant:

1. **File Upload Process**:
   - Filename is generated: `{school_code}_{admission_number}.{ext}`
   - Upload config has `'overwrite' => TRUE`
   - New file automatically replaces old file with same name

2. **Cache Busting Strategy**:
   - **List View**: Uses `&list={random}&ts={timestamp}` parameters
   - **Edit Modal**: Uses `&edit={timestamp}&modal={random}` parameters
   - Different parameters ensure browser doesn't use cached version

3. **Photo Display Flow**:
   ```
   Upload → Replace File → Controller adds cache buster → View displays with unique URL → Browser loads fresh image
   ```

### 4. Participant List Sorting

**New Sort Order**:
1. Primary: Minimum item code (ascending)
2. Secondary: Class (ascending)
3. Tertiary: Participant name (ascending)

This ensures participants are grouped by their items in ascending order.

## Testing Checklist

- [x] Participant list displays photos correctly
- [x] Photos load on page refresh
- [x] Edit modal shows correct photo preview
- [x] Uploading new photo replaces old photo
- [x] Edit modal photo updates after upload
- [x] List photo updates after upload
- [x] Participants sorted by item code (ascending)
- [x] No photo shows default placeholder image
- [x] Photo removal works correctly

## Files Modified

1. `application/controllers/schools/Registration.php`
2. `application/models/schools/Registration_Model.php`
3. `application/views/schools/school_entry.php`

## Technical Notes

### Photo Model (Photos_Model.php)
The `get_Photo()` method searches for photos in this order:
1. Checks for `.jpg` extension
2. Checks for `.jpeg` extension
3. Checks for `.gif` extension
4. Checks for `.png` extension
5. Returns `null` if no photo found

### Cache Busting Strategy
- **Why needed**: Browsers cache images aggressively
- **Solution**: Add unique query parameters to force reload
- **Implementation**: Combination of timestamp and random values
- **Different contexts**: List vs Modal use different parameters

### File Replacement
- Handled automatically by CodeIgniter's upload library
- `'overwrite' => TRUE` in upload config
- Same filename = automatic replacement
- No manual deletion needed

## Future Enhancements

1. Add image compression for better performance
2. Implement thumbnail generation for list view
3. Add photo validation (face detection, size limits)
4. Implement photo cropping interface
5. Add bulk photo upload feature

## Support

For issues or questions, refer to:
- CodeIgniter Upload Library: https://codeigniter.com/userguide3/libraries/file_uploading.html
- Photo storage path: `photos/{sub_district}/{school_code}/`
- Default placeholder: `photos/img_user_nophoto.png`
