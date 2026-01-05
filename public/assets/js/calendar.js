let calendar
let events = []
let currentEventId = null
let hiddenCategories = new Set()
let hiddenStatuses = new Set()
let selectedRoomFilter = ''

const categoryColors = {
  work: '#bc307bff',
  seminar: '#c4b517ff',
  other: '#4d6d41ff'
}

const statusColors = {
  pending: '#ffc107',
  approved: '#28a745'
}

const categoryNames = {
  work: 'Làm việc - nghiên cứu',
  seminar: 'Hội thảo - Seminar',
  other: 'Khác'
}

const roomMap = {}
if (window.LAB_ROOMS && Array.isArray(window.LAB_ROOMS)) {
  window.LAB_ROOMS.forEach((r) => {
    if (r.code) roomMap[r.code] = r.name || r.code
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

      el.style.position = 'relative'
      el.style.willChange = 'transform'
    },

    eventContent: function (arg) {
      const event = arg.event
      const props = event.extendedProps || {}

      const status = props.status || 'pending'
      const roomName = props.roomName || ''
      const isApproved = status === 'approved'
      const statusText = isApproved ? 'Đã duyệt' : 'Chờ duyệt'
      const statusIcon = isApproved
        ? '<i class="fa-solid fa-circle-check"></i>'
        : '<i class="fa-solid fa-clock"></i>'

      const cat = props.category || 'work'
      const catText = categoryNames[cat] || cat

      const color = props._color || event.backgroundColor || '#3788d8'
      const chipBg = isLightColor(color) ? 'rgba(17,24,39,.12)' : 'rgba(255,255,255,.22)'
      const chipBorder = isLightColor(color) ? 'rgba(17,24,39,.16)' : 'rgba(255,255,255,.18)'

      const html = `
        <div class="fc-event-main-custom" style="padding:6px 8px;">
          <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
            <div class="fc-event-time" style="font-weight:800;letter-spacing:.2px;">${arg.timeText || ''}</div>
            <span style="margin-left:auto;display:inline-flex;align-items:center;gap:6px;padding:2px 8px;border-radius:999px;background:${chipBg};border:1px solid ${chipBorder};font-size:10px;font-weight:800;line-height:1;">
              <span class="fc-status-icon" style="font-size:11px;display:inline-flex;align-items:center;">${statusIcon}</span>
              <span>${statusText}</span>
            </span>
          </div>

          <div class="fc-event-title" style="font-weight:800;line-height:1.2;margin-bottom:4px;">
            ${event.title || ''}
          </div>

          <div style="display:flex;flex-wrap:wrap;gap:6px;">
            ${
              roomName
                ? `<span style="display:inline-flex;align-items:center;gap:6px;padding:2px 8px;border-radius:999px;background:${chipBg};border:1px solid ${chipBorder};font-size:10px;font-weight:700;line-height:1;">
                     <i class="fa-solid fa-door-open" style="font-size:10px;"></i>
                     <span>${roomName}</span>
                   </span>`
                : ''
            }

            <span style="display:inline-flex;align-items:center;gap:6px;padding:2px 8px;border-radius:999px;background:${chipBg};border:1px solid ${chipBorder};font-size:10px;font-weight:700;line-height:1;">
              <span style="width:8px;height:8px;border-radius:999px;background:rgba(255,255,255,.75);"></span>
              <span>${catText}</span>
            </span>
          </div>
        </div>
      `
      return { html }
    },

    eventClick: function (info) {
      showEventDetail(info.event)
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
      updateEventTime(info.event)
    },

    eventResize: function (info) {
      const now = new Date()
      if (info.event.start < now) {
        if (window.toastr) toastr.warning('Không thể chuyển sự kiện vào quá khứ')
        info.revert()
        return
      }
      updateEventTime(info.event)
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
}

function setMinDateForInputs() {
  const today = new Date().toISOString().split('T')[0]
  const startDateInput = document.getElementById('eventStartDate')
  const endDateInput = document.getElementById('eventEndDate')
  if (startDateInput) startDateInput.setAttribute('min', today)
  if (endDateInput) endDateInput.setAttribute('min', today)
}

async function loadEvent() {
  try {
    const response = await fetch('/bookings', { headers: { Accept: 'application/json' } })
    const data = await response.json()
    const raw = Array.isArray(data) ? data : data.data || []

    events = raw.map((item) => {
      const category = item.category || 'work'
      const status = item.status || 'pending'
      const safeCategory = categoryColors[category] ? category : 'work'
      const roomCode = item.lab_code || null
      const roomName = roomCode ? roomMap[roomCode] || roomCode : null
      const color = item.color || categoryColors[safeCategory] || '#3788d8'

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
        color: color
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
      const endDate = e.end ? (e.end instanceof Date ? e.end : new Date(e.end)) : new Date(startDate.getTime() + 60 * 60 * 1000)

      const bg = e.color || categoryColors[e.category] || '#3788d8'
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
          _color: bg,
          _textColor: tx
        }
      })
    })
  })
}

function openCreateModal(start = null, end = null) {
  document.getElementById('modalTitle').textContent = 'Tạo sự kiện mới'
  document.getElementById('eventForm').reset()
  document.getElementById('eventId').value = ''
  resetColorPicker()

  const now = new Date()
  const today = now.toISOString().split('T')[0]

  if (start) {
    const startDate = new Date(start)
    if (startDate < now) {
      if (window.toastr) toastr.warning('Không thể tạo sự kiện trong quá khứ')
      return
    }

    document.getElementById('eventStartDate').value = startDate.toISOString().split('T')[0]
    document.getElementById('eventStartTime').value = startDate.toTimeString().slice(0, 5)

    if (end) {
      const endDate = new Date(end)
      document.getElementById('eventEndDate').value = endDate.toISOString().split('T')[0]
      document.getElementById('eventEndTime').value = endDate.toTimeString().slice(0, 5)
    }
  } else {
    document.getElementById('eventStartDate').value = today
    document.getElementById('eventStartTime').value = '09:00'
    document.getElementById('eventEndDate').value = today
    document.getElementById('eventEndTime').value = '10:00'
  }

  document.getElementById('eventModal').classList.add('active')
}

function closeModal() {
  document.getElementById('eventModal').classList.remove('active')
}

async function saveEvent() {
  const id = document.getElementById('eventId').value
  const title = document.getElementById('eventTitle').value.trim()
  const category = document.getElementById('eventCategory').value
  const roomCode = document.getElementById('eventRoom').value.trim()
  const startDate = document.getElementById('eventStartDate').value
  const startTime = document.getElementById('eventStartTime').value
  const endDate = document.getElementById('eventEndDate').value
  const endTime = document.getElementById('eventEndTime').value
  const description = document.getElementById('eventDescription').value.trim()
  const filesInput = document.getElementById('eventFiles')
  const colorInput = document.getElementById('eventColor')
  const selectedColor = colorInput ? colorInput.value : null

  if (!title) {
    if (window.toastr) toastr.error('Vui lòng nhập tiêu đề sự kiện')
    return
  }
  if (!roomCode) {
    if (window.toastr) toastr.error('Vui lòng chọn phòng lab / phòng sử dụng')
    return
  }

  const startDateTime = new Date(`${startDate}T${startTime}`)
  const endDateTime = new Date(`${endDate}T${endTime}`)
  const now = new Date()

  if (!id && startDateTime < now) {
    if (window.toastr) toastr.error('Không thể đăng ký sự kiện trong quá khứ')
    return
  }
  if (endDateTime <= startDateTime) {
    if (window.toastr) toastr.error('Thời gian kết thúc phải sau thời gian bắt đầu')
    return
  }

  const API_URL = '/bookings'
  const formData = new FormData()
  formData.append('title', title)
  formData.append('category', category)
  formData.append('lab_code', roomCode)
  formData.append('start', `${startDate} ${startTime}:00`)
  formData.append('end', `${endDate} ${endTime}:00`)
  formData.append('description', description)

  if (selectedColor) formData.append('color', selectedColor)

  if (filesInput && filesInput.files.length > 0) {
    Array.from(filesInput.files).forEach((file) => formData.append('files[]', file))
  }

  try {
    let url = API_URL
    if (id) {
      url = `${API_URL}/${id}`
      formData.append('_method', 'PUT')
    }

    const response = await fetch(url, {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    })

    const result = await response.json().catch(() => ({}))

    if (!response.ok) {
      if (response.status === 401) {
        if (window.toastr) toastr.error('Bạn cần đăng nhập để đăng ký sự kiện.')
        return
      }

      const msg =
        (result && (result.message || (result.errors && Object.values(result.errors)[0][0]))) ||
        'Có lỗi xảy ra, vui lòng thử lại.'
      if (window.toastr) toastr.error(msg)
      console.error('Save event error: ', result)
      return
    }

    if (window.toastr) toastr.success(result.message || (id ? 'Cập nhật sự kiện thành công.' : 'Tạo sự kiện thành công.'))
    await loadEvent()
    closeModal()
  } catch (error) {
    console.error(error)
    if (window.toastr) toastr.error('Lỗi kết nối máy chủ. Vui lòng thử lại sau.')
  }
}

function showEventDetail(calendarEvent) {
  const props = calendarEvent.extendedProps || {}
  const title = calendarEvent.title
  const startDate = calendarEvent.start
  const endDate = calendarEvent.end
  const category = props.category
  const description = props.description
  const status = props.status
  const roomName = props.roomName || (props.roomCode ? roomMap[props.roomCode] || props.roomCode : null)

  currentEventId = calendarEvent.id

  document.getElementById('detailTime').textContent = `${startDate.toLocaleDateString('vi-VN')} ${startDate.toLocaleTimeString('vi-VN', {
    hour: '2-digit',
    minute: '2-digit'
  })} - ${endDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })}`

  document.getElementById('detailRoom').textContent = roomName || 'Phòng máy trung tâm'

  if (description) {
    document.getElementById('detailDescription').textContent = description
    document.getElementById('detailDescriptionRow').style.display = 'flex'
  } else {
    document.getElementById('detailDescriptionRow').style.display = 'none'
  }

  document.getElementById('detailTitle').textContent = title
  document.getElementById('detailCategory').textContent = categoryNames[category] || category

  const statusTextEl = document.getElementById('detailStatus')
  const pendingIcon = document.getElementById('statusPendingIcon')
  const approvedIcon = document.getElementById('statusApprovedIcon')

  pendingIcon.style.display = 'none'
  approvedIcon.style.display = 'none'

  if (status === 'approved') {
    approvedIcon.style.display = 'inline-block'
    statusTextEl.textContent = 'Đã duyệt'
  } else {
    pendingIcon.style.display = 'inline-block'
    statusTextEl.textContent = 'Chờ duyệt'
  }

  document.getElementById('detailModal').classList.add('active')
}

function closeDetailModal() {
  document.getElementById('detailModal').classList.remove('active')
  currentEventId = null
}

function editEvent() {
  const event = events.find((e) => e.id == currentEventId)
  if (!event) return

  closeDetailModal()

  document.getElementById('modalTitle').textContent = 'Sửa sự kiện'
  document.getElementById('eventId').value = event.id
  document.getElementById('eventTitle').value = event.title
  document.getElementById('eventCategory').value = event.category
  document.getElementById('eventDescription').value = event.description || ''
  document.getElementById('eventRoom').value = event.roomCode || ''

  if (event.color) setColorValue(event.color)
  else resetColorPicker()

  const startDate = new Date(event.start)
  const endDate = new Date(event.end)

  const startLocalDate = `${startDate.getFullYear()}-${String(startDate.getMonth() + 1).padStart(2, '0')}-${String(startDate.getDate()).padStart(2, '0')}`
  const endLocalDate = `${endDate.getFullYear()}-${String(endDate.getMonth() + 1).padStart(2, '0')}-${String(endDate.getDate()).padStart(2, '0')}`

  const startTime = startDate.toTimeString().slice(0, 5)
  const endTime = endDate.toTimeString().slice(0, 5)

  document.getElementById('eventStartDate').value = startLocalDate
  document.getElementById('eventStartTime').value = startTime
  document.getElementById('eventEndDate').value = endLocalDate
  document.getElementById('eventEndTime').value = endTime

  document.getElementById('eventModal').classList.add('active')
}

function deleteEvent() {
  document.getElementById('confirmDeleteModal').classList.add('active')
}

function closeConfirmDelete() {
  document.getElementById('confirmDeleteModal').classList.remove('active')
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

    const result = await response.json().catch(() => ({}))

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

async function updateEventTime(calendarEvent) {
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
    return `${year}-${month}-${day}T${hour}:${minute}:${second}`
  }

  const start = formatLocalDateTime(startDate)
  const end = formatLocalDateTime(endDate)

  const payload = {
    title: calendarEvent.title,
    category: props.category || 'work',
    lab_code: props.roomCode || props.lab_code || null,
    description: props.description || '',
    start,
    end
  }

  if (!payload.lab_code) {
    if (window.toastr) toastr.error('Sự kiện không có thông tin phòng, không thể cập nhật thời gian.')
    calendarEvent.revert()
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

    const result = await response.json().catch(() => ({}))

    if (!response.ok) {
      const msg =
        (result && (result.message || (result.errors && Object.values(result.errors)[0][0]))) ||
        'Không thể cập nhật thời gian.'
      if (window.toastr) toastr.error(msg)
      calendarEvent.revert()
      return
    }

    if (window.toastr) toastr.success(result.message || 'Đã cập nhật thời gian sự kiện.')

    const eventIndex = events.findIndex((e) => e.id == id)
    if (eventIndex !== -1) {
      events[eventIndex] = { ...events[eventIndex], start: start, end: end }
      const calEvent = calendar.getEventById(id)
      if (calEvent) {
        calEvent.setStart(startDate)
        calEvent.setEnd(endDate)
      }
    }
  } catch (err) {
    console.error(err)
    if (window.toastr) toastr.error('Lỗi kết nối khi cập nhật.')
    calendarEvent.revert()
  }
}

function resetColorPicker() {
  const colorInput = document.getElementById('eventColor')
  const customBtn = document.getElementById('customColorWrapper')

  if (colorInput) colorInput.value = '#d50000'

  document.querySelectorAll('.color-circle').forEach((c) => c.classList.remove('active'))

  const firstColor = document.querySelector('.color-circle[data-color="#d50000"]')
  if (firstColor) firstColor.classList.add('active')

  if (customBtn) {
    customBtn.style.backgroundColor = '#fff'
    customBtn.classList.remove('has-value', 'active')

    const checkIcon = customBtn.querySelector('.fa-check')
    if (checkIcon) checkIcon.remove()

    const plusIcon = customBtn.querySelector('.fa-plus')
    if (plusIcon) plusIcon.style.display = 'block'
  }
}

function setColorValue(color) {
  const colorInput = document.getElementById('eventColor')
  const customBtn = document.getElementById('customColorWrapper')

  if (colorInput) colorInput.value = color

  document.querySelectorAll('.color-circle').forEach((c) => c.classList.remove('active'))

  const presetColor = document.querySelector(`.color-circle[data-color="${color}"]`)
  if (presetColor) {
    presetColor.classList.add('active')
    if (customBtn) {
      customBtn.style.backgroundColor = '#fff'
      customBtn.classList.remove('has-value', 'active')
      const checkIcon = customBtn.querySelector('.fa-check')
      if (checkIcon) checkIcon.remove()
      const plusIcon = customBtn.querySelector('.fa-plus')
      if (plusIcon) plusIcon.style.display = 'block'
    }
  } else {
    if (customBtn) {
      customBtn.classList.add('active', 'has-value')
      customBtn.style.backgroundColor = color

      const plusIcon = customBtn.querySelector('.fa-plus')
      if (plusIcon) plusIcon.style.display = 'none'

      let checkIcon = customBtn.querySelector('.fa-check')
      if (!checkIcon) {
        checkIcon = document.createElement('i')
        checkIcon.className = 'fa-solid fa-check'
        customBtn.appendChild(checkIcon)
      }
      checkIcon.style.color = isLightColor(color) ? '#3c4043' : '#fff'
    }
  }
}

document.addEventListener('DOMContentLoaded', function () {
  initCalendar()

  const colorCircles = document.querySelectorAll('.color-circle:not(.custom-color-btn)')
  const customBtn = document.getElementById('customColorWrapper')
  const colorInput = document.getElementById('eventColor')

  colorCircles.forEach((circle) => {
    circle.addEventListener('click', function () {
      const color = this.getAttribute('data-color')
      setColorValue(color)
    })
  })

  if (customBtn && colorInput) {
    customBtn.addEventListener('click', function () {
      colorInput.click()
    })

    colorInput.addEventListener('input', function () {
      setColorValue(this.value)
    })
  }
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
