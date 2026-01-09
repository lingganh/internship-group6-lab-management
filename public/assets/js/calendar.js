let calendar
let events = []
let currentEventId = null
let hiddenCategories = new Set()
let hiddenStatuses = new Set()
let selectedRoomFilter = ''

// Màu cố định theo TRẠNG THÁI
const statusColors = {
  pending: '#f59e0b',   // vàng cam
  approved: '#10b981',  // xanh lá
  completed: '#6366f1', // xanh tím
  cancelled: '#ef4444'  // đỏ
}

// fallback theo loại (dùng cho legend / khi không có status)
const categoryColors = {
  work: '#bc307bff',
  seminar: '#c4b517ff',
  other: '#4d6d41ff'
}

const categoryNames = {
  work: 'Làm việc - nghiên cứu',
  seminar: 'Hội thảo - Seminar',
  other: 'Khác'
}

const roomMap = {}
if (window.LAB_ROOMS && Array.isArray(window.LAB_ROOMS)) {
  window.LAB_ROOMS.forEach((r) => {
    if (r.code != null) roomMap[String(r.code)] = r.name || String(r.code)
  })
}

const groupMap = {}
if (window.LAB_GROUPS && Array.isArray(window.LAB_GROUPS)) {
  window.LAB_GROUPS.forEach((g) => {
    if (g.id != null) groupMap[String(g.id)] = g.name || String(g.id)
  })
}

function normalizeDateString(v) {
  if (!v) return null
  if (v instanceof Date) return v
  if (typeof v !== 'string') return v
  return v.includes('T') ? v : v.replace(' ', 'T')
}

function hexToRgb(color) {
  if (!color || typeof color !== 'string') return null
  let c = color.trim()
  if (c.startsWith('rgb')) return null
  if (c[0] === '#') c = c.slice(1)
  if (c.length === 3) c = c.split('').map((x) => x + x).join('')
  if (c.length === 8) c = c.slice(0, 6)
  if (c.length !== 6) return null
  const r = parseInt(c.slice(0, 2), 16)
  const g = parseInt(c.slice(2, 4), 16)
  const b = parseInt(c.slice(4, 6), 16)
  if ([r, g, b].some((n) => Number.isNaN(n))) return null
  return { r, g, b }
}

function isLightColor(color) {
  const rgb = hexToRgb(color)
  if (!rgb) return false
  const { r, g, b } = rgb
  const brightness = (r * 299 + g * 587 + b * 114) / 1000
  return brightness > 155
}

function readableTextColor(bg) {
  return isLightColor(bg) ? '#1f2937' : '#ffffff'
}

function initCalendar() {
  initMiniCalendar()

  const calendarEl = document.getElementById('calendar')
  if (!calendarEl) return

  const canCreate = window.LAB_USER && window.LAB_USER.logged_in

  calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'timeGridWeek',
    locale: 'vi',
    firstDay: 1,
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    buttonText: {
      today: 'Hôm nay',
      month: 'Tháng',
      week: 'Tuần',
      day: 'Ngày'
    },
    slotMinTime: '07:00:00',
    slotMaxTime: '19:00:00',
    allDaySlot: false,
    nowIndicator: true,
    editable: true,
    selectMirror: true,
    selectable: canCreate,
    dayMaxEvents: true,
    weekends: true,
    height: 'auto',
    selectConstraint: {
      start: new Date().toISOString().split('T')[0]
    },

    eventAllow: function (dropInfo, draggedEvent) {
      if (!checkPermission(draggedEvent)) return false
      const now = new Date()
      if (dropInfo.start && dropInfo.start < now) return false
      return true
    },

    eventDidMount: function (info) {
      const el = info.el
      const props = info.event.extendedProps || {}
      const status = props.status || 'pending'
      const color = props._color || info.event.backgroundColor || '#3788d8'
      const textColor = props._textColor || readableTextColor(color)

      el.style.setProperty('--fc-event-bg-color', color)
      el.style.setProperty('--fc-event-border-color', color)
      el.style.setProperty('--fc-event-text-color', textColor)

      if (status === 'pending') el.classList.add('is-pending')
      else el.classList.remove('is-pending')

      const canEdit = checkPermission(info.event)
      if (!canEdit) el.classList.add('is-no-edit')
      else el.classList.remove('is-no-edit')

      el.style.position = 'relative'
      el.style.willChange = 'transform'
    },

    // CONTENT: bỏ chip loại sự kiện, badge trạng thái chỉ icon
    eventContent: function (arg) {
      const event = arg.event
      const props = event.extendedProps || {}

      const status = props.status || 'pending'
      const roomName = props.roomName || ''

      let statusIcon = '<i class="fa-solid fa-clock"></i>'
      let statusTitle = 'Chờ duyệt'

      if (status === 'approved') {
        statusIcon = '<i class="fa-solid fa-circle-check"></i>'
        statusTitle = 'Đã duyệt'
      } else if (status === 'completed') {
        statusIcon = '<i class="fa-solid fa-check-double"></i>'
        statusTitle = 'Đã hoàn thành'
      } else if (status === 'cancelled') {
        statusIcon = '<i class="fa-solid fa-ban"></i>'
        statusTitle = 'Đã hủy'
      }

      const color = props._color || event.backgroundColor || '#3788d8'
      const chipBg = isLightColor(color) ? 'rgba(17,24,39,.12)' : 'rgba(255,255,255,.22)'
      const chipBorder = isLightColor(color) ? 'rgba(17,24,39,.16)' : 'rgba(255,255,255,.18)'

      const html = `
        <div class="fc-event-main-custom" style="padding:6px 8px;">
          <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
            <div class="fc-event-time" style="font-weight:800;letter-spacing:.2px;">
              ${arg.timeText || ''}
            </div>

            <!-- Badge trạng thái chỉ icon -->
            <span
              title="${statusTitle}"
              style="
                margin-left:auto;
                display:inline-flex;
                align-items:center;
                justify-content:center;
                width:22px;
                height:22px;
                border-radius:999px;
                background:${chipBg};
                border:1px solid ${chipBorder};
                font-size:11px;
              "
            >
              ${statusIcon}
            </span>
          </div>

          <div class="fc-event-title" style="font-weight:800;line-height:1.2;margin-bottom:4px;">
            ${event.title || ''}
          </div>

          ${
            roomName
              ? `<div style="display:flex;align-items:center;gap:6px;font-size:10px;">
                   <i class="fa-solid fa-door-open" style="font-size:10px;"></i>
                   <span>${roomName}</span>
                 </div>`
              : ''
          }
        </div>
      `
      return { html }
    },

    eventClick: function (info) {
      showEventDetails(info.event)
    },

    select: function (info) {
      const now = new Date()
      if (info.start < now) {
        if (window.toastr) toastr.warning('Không thể đăng ký sự kiện trong quá khứ')
        calendar.unselect()
        return
      }
      openCreateModal(info.start, info.end)
    },

    eventDrop: function (info) {
      const now = new Date()
      if (info.event.start < now) {
        if (window.toastr) toastr.warning('Không thể chuyển sự kiện vào quá khứ')
        info.revert()
        return
      }
      updateEventTime(info.event, info)
    },

    eventResize: function (info) {
      const now = new Date()
      if (info.event.start < now) {
        if (window.toastr) toastr.warning('Không thể chuyển sự kiện vào quá khứ')
        info.revert()
        return
      }
      updateEventTime(info.event, info)
    }
  })

  initFiltersAndButtons()
  loadEvent()
  calendar.render()
}

function initFiltersAndButtons() {
  const statusCheckboxes = document.querySelectorAll('[data-filter-status]')
  statusCheckboxes.forEach((cb) => {
    const status = cb.getAttribute('data-filter-status')
    if (!cb.checked) hiddenStatuses.add(status)

    cb.addEventListener('change', function () {
      if (this.checked) hiddenStatuses.delete(status)
      else hiddenStatuses.add(status)
      updateCalendar()
    })
  })

  const categoryCheckboxes = document.querySelectorAll('[data-filter-category]')
  categoryCheckboxes.forEach((cb) => {
    const cat = cb.getAttribute('data-filter-category')
    if (!cb.checked) hiddenCategories.add(cat)

    cb.addEventListener('change', function () {
      if (this.checked) hiddenCategories.delete(cat)
      else hiddenCategories.add(cat)
      updateCalendar()
    })
  })

  const roomFilterSelect = document.getElementById('labRoomFilter')
  if (roomFilterSelect) {
    roomFilterSelect.addEventListener('change', function () {
      selectedRoomFilter = this.value || ''
      updateCalendar()
    })
  }

  const createBtn = document.querySelector('.js-open-create-event')
  if (createBtn) {
    createBtn.addEventListener('click', function () {
      openCreateModal()
    })
  }

  setMinDateForInputs()
  initRepeatControls()
}

function setMinDateForInputs() {
  const today = new Date().toISOString().split('T')[0]
  const startDateInput = document.getElementById('eventStartDate')
  const endDateInput = document.getElementById('eventEndDate')
  const repeatUntilInput = document.getElementById('eventRepeatUntil')
  if (startDateInput) startDateInput.setAttribute('min', today)
  if (endDateInput) endDateInput.setAttribute('min', today)
  if (repeatUntilInput) repeatUntilInput.setAttribute('min', today)
}

function initRepeatControls() {
  const repeatTypeSelect = document.getElementById('eventRepeatType')
  const weekdaySection = document.getElementById('weekdaySection')

  if (!repeatTypeSelect || !weekdaySection) return

  repeatTypeSelect.addEventListener('change', function () {
    if (this.value === 'weekly') {
      weekdaySection.style.display = 'block'
    } else {
      weekdaySection.style.display = 'none'
      document.querySelectorAll('.repeat-day-checkbox').forEach((cb) => {
        cb.checked = false
      })
    }
  })
}

async function loadEvent() {
  try {
    const response = await fetch('/bookings', { headers: { Accept: 'application/json' } })
    const text = await response.text()

    let data = null
    try {
      data = JSON.parse(text)
    } catch (e) {
      console.error('loadEvent not JSON:', response.status, text)
      if (window.toastr) toastr.error('Không tải được dữ liệu lịch (response không phải JSON).')
      return
    }

    if (!response.ok) {
      console.error('loadEvent not ok:', response.status, data)
      if (window.toastr) toastr.error((data && data.message) || 'Không tải được dữ liệu lịch.')
      return
    }

    const raw = Array.isArray(data) ? data : data.data || []

    events = raw.map((item) => {
      const category = item.category || 'work'
      const status = item.status || 'pending'
      const safeCategory = categoryColors[category] ? category : 'work'

      const roomCode = item.lab_code != null ? String(item.lab_code) : null
      const roomName = roomCode ? roomMap[roomCode] || roomCode : null

      const bgColor = statusColors[status] || categoryColors[safeCategory] || '#3788d8'

      const registeredFor = item.registered_for != null ? String(item.registered_for) : ''
      const registeredForName = registeredFor ? groupMap[registeredFor] || registeredFor : ''

      return {
        id: item.id,
        title: item.title,
        start: normalizeDateString(item.start),
        end: normalizeDateString(item.end),
        category: safeCategory,
        description: item.description,
        status: status,
        roomCode: roomCode,
        roomName: roomName,
        color: bgColor,
        user_id: item.user_id || item.userId || null,
        registered_for: registeredFor,
        registeredForName: registeredForName
      }
    })

    updateCalendar()
  } catch (err) {
    console.error(err)
    if (window.toastr) toastr.error('Không tải được dữ liệu lịch.')
    else alert('Không tải được dữ liệu lịch.')
  }
}

function updateCalendar() {
  if (!calendar) return

  calendar.batchRendering(() => {
    calendar.removeAllEvents()

    const visibleEvents = events.filter(
      (e) =>
        !hiddenCategories.has(e.category) &&
        !hiddenStatuses.has(e.status) &&
        (!selectedRoomFilter || e.roomCode === selectedRoomFilter)
    )

    visibleEvents.forEach((e) => {
      const startDate = e.start instanceof Date ? e.start : new Date(e.start)
      const endDate = e.end
        ? e.end instanceof Date
          ? e.end
          : new Date(e.end)
        : new Date(startDate.getTime() + 60 * 60 * 1000)

      const bg = statusColors[e.status] || e.color || categoryColors[e.category] || '#3788d8'
      const tx = readableTextColor(bg)

      calendar.addEvent({
        id: e.id,
        title: e.title,
        start: startDate,
        end: endDate,
        backgroundColor: bg,
        borderColor: bg,
        textColor: tx,
        extendedProps: {
          category: e.category,
          description: e.description,
          status: e.status,
          roomCode: e.roomCode,
          roomName: e.roomName,
          user_id: e.user_id,
          registered_for: e.registered_for,
          registeredForName: e.registeredForName,
          _color: bg,
          _textColor: tx
        }
      })
    })
  })
}

function openCreateModal(start = null, end = null) {
  const form = document.getElementById('eventForm')
  if (form) form.reset()

  document.getElementById('modalTitle').textContent = 'Tạo sự kiện mới'
  document.getElementById('eventId').value = ''

  const now = new Date()
  const today = now.toISOString().split('T')[0]

  const startDateInput = document.getElementById('eventStartDate')
  const startTimeInput = document.getElementById('eventStartTime')
  const endDateInput = document.getElementById('eventEndDate')
  const endTimeInput = document.getElementById('eventEndTime')

  if (start) {
    const startDate = new Date(start)
    if (startDate < now) {
      if (window.toastr) toastr.warning('Không thể tạo sự kiện trong quá khứ')
      return
    }

    if (startDateInput) startDateInput.value = startDate.toISOString().split('T')[0]
    if (startTimeInput) startTimeInput.value = startDate.toTimeString().slice(0, 5)

    if (end) {
      const endDate = new Date(end)
      if (endDateInput) endDateInput.value = endDate.toISOString().split('T')[0]
      if (endTimeInput) endTimeInput.value = endDate.toTimeString().slice(0, 5)
    } else {
      const endAuto = new Date(startDate.getTime() + 60 * 60 * 1000)
      if (endDateInput) endDateInput.value = endAuto.toISOString().split('T')[0]
      if (endTimeInput) endTimeInput.value = endAuto.toTimeString().slice(0, 5)
    }
  } else {
    if (startDateInput) startDateInput.value = today
    if (startTimeInput) startTimeInput.value = '09:00'
    if (endDateInput) endDateInput.value = today
    if (endTimeInput) endTimeInput.value = '10:00'
  }

  // reset repeat
  const repeatTypeSelect = document.getElementById('eventRepeatType')
  const repeatUntilInput = document.getElementById('eventRepeatUntil')
  const weekdaySection = document.getElementById('weekdaySection')
  if (repeatTypeSelect) repeatTypeSelect.value = ''
  if (repeatUntilInput) repeatUntilInput.value = ''
  if (weekdaySection) {
    weekdaySection.style.display = 'none'
    document.querySelectorAll('.repeat-day-checkbox').forEach((cb) => {
      cb.checked = false
    })
  }

  const modal = document.getElementById('eventModal')
  if (modal) modal.classList.add('active')
}

function closeModal() {
  const modal = document.getElementById('eventModal')
  if (modal) modal.classList.remove('active')
}

async function saveEvent() {
  const eventId = document.getElementById('eventId').value
  const title = document.getElementById('eventTitle').value.trim()
  const category = document.getElementById('eventCategory').value
  const color = document.getElementById('eventColor').value
  const labCode = document.getElementById('eventRoom').value
  const registeredFor = document.getElementById('eventRegisteredFor').value.trim()

  const startDate = document.getElementById('eventStartDate').value
  const startTime = document.getElementById('eventStartTime').value
  const endDate = document.getElementById('eventEndDate').value
  const endTime = document.getElementById('eventEndTime').value
  const description = document.getElementById('eventDescription').value.trim()

  const repeatType = document.getElementById('eventRepeatType').value
  const repeatUntil = document.getElementById('eventRepeatUntil').value
  const repeatDays = Array.from(document.querySelectorAll('.repeat-day-checkbox:checked')).map((cb) => cb.value)

  if (!title || !labCode || !startDate || !startTime || !endDate || !endTime) {
    toastr.error('Vui lòng điền đầy đủ thông tin bắt buộc.')
    return
  }

  const start = `${startDate} ${startTime}:00`
  const end = `${endDate} ${endTime}:00`

  const formData = new FormData()
  formData.append('title', title)
  formData.append('category', category)
  formData.append('color', color)
  formData.append('lab_code', labCode)
  formData.append('start', start)
  formData.append('end', end)
  formData.append('description', description)
  formData.append('registered_for', registeredFor)

  // gửi thông tin lặp giống bên admin register
  formData.append('repeat_type', repeatType || '')
  formData.append('repeat_until', repeatUntil || '')
  repeatDays.forEach((d) => formData.append('repeat_days[]', d))

  const filesEl = document.getElementById('eventFiles')
  const files = filesEl ? filesEl.files : null
  if (files && files.length > 0) {
    for (let i = 0; i < files.length; i++) formData.append('files[]', files[i])
  }

  const url = eventId ? `/bookings/${eventId}` : '/bookings'
  if (eventId) formData.append('_method', 'PUT')

  fetch(url, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
  })
    .then(async (response) => {
      const text = await response.text()
      let data = null
      try {
        data = JSON.parse(text)
      } catch (e) {
        console.error('saveEvent not JSON:', response.status, text)
        toastr.error('Có lỗi xảy ra khi lưu sự kiện (response không phải JSON).')
        return null
      }

      if (!response.ok) {
        toastr.error((data && data.message) || 'Có lỗi xảy ra khi lưu sự kiện.')
        return null
      }

      return data
    })
    .then(async (data) => {
      if (!data) return
      if (data.message) toastr.success(data.message)
      closeModal()
      await loadEvent()
    })
    .catch((error) => {
      console.error('Error:', error)
      toastr.error('Có lỗi xảy ra khi lưu sự kiện.')
    })
}

function showEventDetails(eventData) {
  currentEventId = eventData.id

  document.getElementById('detailTitle').textContent = eventData.title
  document.getElementById('detailTime').textContent = `${formatDateTime(eventData.start)} - ${formatDateTime(
    eventData.end
  )}`

  const roomName = eventData.extendedProps.roomName || ''
  document.getElementById('detailRoom').textContent = roomName

  const registeredForRow = document.getElementById('detailRegisteredForRow')
  const registeredForSpan = document.getElementById('detailRegisteredFor')

  const rfName = eventData.extendedProps.registeredForName || ''
  if (rfName) {
    registeredForSpan.textContent = rfName
    registeredForRow.style.display = 'flex'
  } else {
    registeredForRow.style.display = 'none'
  }

  const descRow = document.getElementById('detailDescriptionRow')
  const descSpan = document.getElementById('detailDescription')
  if (eventData.extendedProps.description) {
    descSpan.textContent = eventData.extendedProps.description
    descRow.style.display = 'flex'
  } else {
    descRow.style.display = 'none'
  }

  const categoryLabels = {
    work: 'Làm việc / nghiên cứu',
    seminar: 'Hội thảo / seminar',
    other: 'Khác'
  }
  document.getElementById('detailCategory').textContent =
    categoryLabels[eventData.extendedProps.category] || eventData.extendedProps.category

  const statusLabels = {
    pending: 'Chờ duyệt',
    approved: 'Đã duyệt',
    completed: 'Đã hoàn thành',
    cancelled: 'Đã hủy'
  }
  const status = eventData.extendedProps.status || 'pending'
  document.getElementById('detailStatus').textContent = statusLabels[status] || status

  const pendingIcon = document.getElementById('statusPendingIcon')
  const approvedIcon = document.getElementById('statusApprovedIcon')
  const completedIcon = document.getElementById('statusCompletedIcon')

  if (pendingIcon) pendingIcon.style.display = status === 'pending' ? 'inline' : 'none'
  if (approvedIcon) approvedIcon.style.display = status === 'approved' ? 'inline' : 'none'
  if (completedIcon) completedIcon.style.display = status === 'completed' ? 'inline' : 'none'

  const canEdit = checkPermission(eventData)
  const editBtn = document.getElementById('editEventBtn')
  const deleteBtn = document.getElementById('deleteEventBtn')

  if (editBtn) editBtn.style.display = canEdit ? 'inline-flex' : 'none'
  if (deleteBtn) deleteBtn.style.display = canEdit ? 'inline-flex' : 'none'

  const modal = document.getElementById('detailModal')
  if (modal) modal.classList.add('active')
}

function closeDetailModal() {
  const modal = document.getElementById('detailModal')
  if (modal) modal.classList.remove('active')
}

function deleteEvent() {
  const eventData = calendar.getEventById(currentEventId)
  if (!eventData) return

  if (!checkPermission(eventData)) {
    toastr.error('Bạn không có quyền xóa sự kiện này.')
    return
  }

  closeDetailModal()
  const confirmModal = document.getElementById('confirmDeleteModal')
  if (confirmModal) confirmModal.classList.add('active')
}

function formatDateTime(dateString) {
  const date = dateString instanceof Date ? dateString : new Date(dateString)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  return `${day}/${month}/${year} ${hours}:${minutes}`
}

function closeConfirmDelete() {
  const modal = document.getElementById('confirmDeleteModal')
  if (modal) modal.classList.remove('active')
}

async function confirmDelete() {
  closeConfirmDelete()

  try {
    const response = await fetch('/bookings/' + currentEventId, {
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })

    const text = await response.text()
    let result = null
    try {
      result = JSON.parse(text)
    } catch (e) {
      console.error('confirmDelete not JSON:', response.status, text)
      if (window.toastr) toastr.error('Không thể xóa sự kiện (response không phải JSON).')
      return
    }

    if (!response.ok) {
      if (window.toastr) toastr.error(result.message || 'Không thể xóa sự kiện.')
      return
    }

    if (window.toastr) toastr.success(result.message || 'Đã xóa sự kiện.')
    await loadEvent()
    closeDetailModal()
  } catch (err) {
    console.error(err)
    if (window.toastr) toastr.error('Lỗi kết nối máy chủ.')
  }
}

async function updateEventTime(calendarEvent, infoCtx = null) {
  const id = calendarEvent.id
  const props = calendarEvent.extendedProps || {}

  const startDate = calendarEvent.start
  const endDate = calendarEvent.end || new Date(startDate.getTime() + 60 * 60 * 1000)

  const formatLocalDateTime = (date) => {
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    const hour = String(date.getHours()).padStart(2, '0')
    const minute = String(date.getMinutes()).padStart(2, '0')
    const second = String(date.getSeconds()).padStart(2, '0')
    return `${year}-${month}-${day} ${hour}:${minute}:${second}`
  }

  const start = formatLocalDateTime(startDate)
  const end = formatLocalDateTime(endDate)

  const payload = {
    title: calendarEvent.title,
    category: props.category || 'work',
    lab_code: props.roomCode || null,
    description: props.description || '',
    start,
    end
  }

  if (!payload.lab_code) {
    if (window.toastr) toastr.error('Sự kiện không có thông tin phòng, không thể cập nhật thời gian.')
    if (infoCtx && typeof infoCtx.revert === 'function') infoCtx.revert()
    return
  }

  try {
    const response = await fetch(`/bookings/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(payload)
    })

    const text = await response.text()
    let result = null
    try {
      result = JSON.parse(text)
    } catch (e) {
      console.error('updateEventTime not JSON:', response.status, text)
      if (window.toastr) toastr.error('Không thể cập nhật thời gian (response không phải JSON).')
      if (infoCtx && typeof infoCtx.revert === 'function') infoCtx.revert()
      return
    }

    if (!response.ok) {
      const msg =
        (result &&
          (result.message ||
            (result.errors && Object.values(result.errors)[0] && Object.values(result.errors)[0][0]))) ||
        'Không thể cập nhật thời gian.'
      if (window.toastr) toastr.error(msg)
      if (infoCtx && typeof infoCtx.revert === 'function') infoCtx.revert()
      return
    }

    if (window.toastr) toastr.success(result.message || 'Đã cập nhật thời gian sự kiện.')
    await loadEvent()
  } catch (err) {
    console.error(err)
    if (window.toastr) toastr.error('Lỗi kết nối khi cập nhật.')
    if (infoCtx && typeof infoCtx.revert === 'function') infoCtx.revert()
  }
}

// khởi tạo
document.addEventListener('DOMContentLoaded', function () {
  initCalendar()
})

const eventModalEl = document.getElementById('eventModal')
if (eventModalEl) {
  eventModalEl.addEventListener('click', function (e) {
    if (e.target === this) closeModal()
  })
}

const detailModalEl = document.getElementById('detailModal')
if (detailModalEl) {
  detailModalEl.addEventListener('click', function (e) {
    if (e.target === this) closeDetailModal()
  })
}

const confirmDeleteEl = document.getElementById('confirmDeleteModal')
if (confirmDeleteEl) {
  confirmDeleteEl.addEventListener('click', function (e) {
    if (e.target === this) closeConfirmDelete()
  })
}

function initMiniCalendar() {
  const miniEl = document.getElementById('miniCalendar')
  if (!miniEl) return

  const mini = new FullCalendar.Calendar(miniEl, {
    initialView: 'dayGridMonth',
    locale: 'vi',
    firstDay: 0,
    headerToolbar: {
      left: 'prev',
      center: 'title',
      right: 'next'
    },
    buttonText: {
      prev: '‹',
      next: '›'
    },
    height: 'auto',
    contentHeight: 'auto',
    expandRows: true,
    fixedWeekCount: false,
    showNonCurrentDates: true,
    selectable: false,
    dayMaxEvents: false,
    navLinks: false,
    dateClick: function (info) {
      if (calendar) calendar.gotoDate(info.date)
    }
  })

  mini.render()
}

function checkPermission(eventData) {
  if (!window.LAB_USER || !window.LAB_USER.logged_in) return false

  const u = window.LAB_USER

  const isAdmin =
    u.is_admin === true ||
    String(u.role_id) === '1' ||
    String(u.roleId || '') === '1' ||
    String(u.role || '') === '1'

  if (isAdmin) return true

  const props = eventData.extendedProps || {}
  const isOwner = props.user_id != null && String(props.user_id) === String(u.user_id)
  if (!isOwner) return false

  if (props.status === 'approved') {
    const eventStart = new Date(eventData.start)
    const now = new Date()
    if (eventStart <= now) return false
  }

  return true
}
