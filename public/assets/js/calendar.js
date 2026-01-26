let calendar
let events = []
let currentEventId = null
let hiddenCategories = new Set()
let hiddenStatuses = new Set()
let selectedRoomFilter = ''

// Màu cố định theo TRẠNG THÁI
const statusColors = {
  pending: '#f59e0b',
  approved: '#10b981',
  completed: '#6366f1',
  cancelled: '#ef4444'
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

/* ======================= INIT CALENDAR ======================= */

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
 console.log('Event:', info.event.title)
  console.log('Start:', info.event.start)
  console.log('End:', info.event.end)
  console.log('Element height:', info.el.offsetHeight, 'px')
  console.log('Harness height:', info.el.closest('.fc-timegrid-event-harness')?.offsetHeight, 'px')
   
},

   eventContent: function (arg) {
  const event = arg.event
  const props = event.extendedProps || {}
  const status = props.status || 'pending'
  const category = props.category || 'work'

  // Tính thời lượng event (phút)
  const durationMinutes = (event.end - event.start) / (1000 * 60)
  
  // Phân loại event theo độ dài
  const isTiny = durationMinutes <= 30      // <= 30 phút: Chỉ hiển thị time + title (1 dòng)
  const isShort = durationMinutes <= 60     // <= 1 tiếng: Time + title + icon status
  const isMedium = durationMinutes <= 90    // <= 1.5 tiếng: + category icon
  // > 90 phút: Hiển thị đầy đủ tất cả

  // Icon trạng thái
  let statusIcon = '<i class="fa-solid fa-clock"></i>'
  if (status === 'approved') statusIcon = '<i class="fa-solid fa-circle-check"></i>'
  else if (status === 'completed') statusIcon = '<i class="fa-solid fa-check-double"></i>'
  else if (status === 'cancelled') statusIcon = '<i class="fa-solid fa-ban"></i>'

  // Icon loại sự kiện
  let categoryIcon = '<i class="fa-solid fa-briefcase"></i>'
  let categoryText = 'Làm việc / Nghiên cứu'
  
  if (category === 'seminar') {
    categoryIcon = '<i class="fa-solid fa-chalkboard-user"></i>'
    categoryText = 'Hội thảo / Seminar'
  } else if (category === 'other') {
    categoryIcon = '<i class="fa-solid fa-ellipsis"></i>'
    categoryText = 'Khác'
  }

  const color = props._color || event.backgroundColor || '#3788d8'
  const chipBg = isLightColor(color) ? 'rgba(17,24,39,.12)' : 'rgba(255,255,255,.22)'
  const chipBorder = isLightColor(color) ? 'rgba(17,24,39,.16)' : 'rgba(255,255,255,.18)'

  let html = ''

  // === 1. EVENT CỰC NGẮN (<= 30 phút): Chỉ time + title inline ===
  if (isTiny) {
    html = `
      <div class="fc-event-main-custom fc-event-tiny" style="padding:4px 6px;overflow:hidden;height:100%;display:flex;align-items:center;gap:6px;">
        <div style="font-weight:800;font-size:10px;white-space:nowrap;">
          ${arg.timeText || ''}
        </div>
        <div style="font-weight:700;font-size:10px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">
          ${event.title || ''}
        </div>
      </div>
    `
  }
  
  // === 2. EVENT NGẮN (<= 1 tiếng): Time + title + status icon ===
  else if (isShort) {
    html = `
      <div class="fc-event-main-custom fc-event-short" style="padding:5px 7px;overflow:hidden;height:100%;">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;">
          <div style="font-weight:800;font-size:10px;letter-spacing:.2px;">
            ${arg.timeText || ''}
          </div>
          <span style="margin-left:auto;display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:999px;background:${chipBg};border:1px solid ${chipBorder};font-size:9px;">
            ${statusIcon}
          </span>
        </div>
        <div style="font-weight:700;line-height:1.2;font-size:12px;;text-overflow:ellipsis;white-space:nowrap;">
          ${event.title || ''}
        </div>
      </div>
    `
  }
  
  // === 3. EVENT VỪA (<= 1.5 tiếng): + category icon ===
  else if (isMedium) {
    html = `
      <div class="fc-event-main-custom fc-event-medium" style="padding:6px 8px;overflow:hidden;height:100%;">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;">
          <div style="font-weight:800;font-size:11px;letter-spacing:.2px;">
            ${arg.timeText || ''}
          </div>
          <span style="margin-left:auto;display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:999px;background:${chipBg};border:1px solid ${chipBorder};font-size:10px;">
            ${statusIcon}
          </span>
        </div>
        <div style="font-weight:800;line-height:1.2;margin-bottom:3px;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
          ${event.title || ''}
        </div>
        <div style="display:flex;align-items:center;gap:5px;font-size:10px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
          ${categoryIcon}
          <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${categoryText}</span>
        </div>
      </div>
    `
  }
  
  // === 4. EVENT DÀI (> 1.5 tiếng): Hiển thị đầy đủ ===
  else {
    html = `
      <div class="fc-event-main-custom fc-event-full" style="padding:6px 8px;overflow:hidden;height:100%;">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
          <div class="fc-event-time" style="font-weight:800;letter-spacing:.2px;font-size:11px;">
            ${arg.timeText || ''}
          </div>
          <span style="margin-left:auto;display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:999px;background:${chipBg};border:1px solid ${chipBorder};font-size:11px;">
            ${statusIcon}
          </span>
        </div>

        <div class="fc-event-title" style="font-weight:800;line-height:1.2;margin-bottom:4px;font-size:15px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
          ${event.title || ''}
        </div>

        <div style="display:flex;align-items:center;gap:6px;font-size:10px;margin-bottom:3px;white-space:nowrap;">
          ${categoryIcon}
          <span style="white-space:nowrap;">${categoryText}</span>
        </div>
        

      </div>
    `
  }

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

/* ======================= FILTERS / BUTTONS ======================= */

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
  const weekSummary = document.getElementById('weekSummary')

  if (!repeatTypeSelect || !weekdaySection) return

  repeatTypeSelect.addEventListener('change', function () {
    if (this.value === 'weekly') {
      weekdaySection.style.display = 'block'
    } else {
      weekdaySection.style.display = 'none'
      document.querySelectorAll('.weekday-checkbox').forEach((cb) => {
        cb.checked = false
      })
      if (weekSummary) weekSummary.textContent = ''
    }

    buildOccurrencesFromForm({ preview: true })
  })

  const repeatUntilInput = document.getElementById('eventRepeatUntil')
  if (repeatUntilInput) {
    repeatUntilInput.addEventListener('change', () => buildOccurrencesFromForm({ preview: true }))
  }

  document.querySelectorAll('.weekday-checkbox').forEach((cb) => {
    cb.addEventListener('change', () => buildOccurrencesFromForm({ preview: true }))
  })

  const startDateInput = document.getElementById('eventStartDate')
  const startTimeInput = document.getElementById('eventStartTime')
  const endTimeInput = document.getElementById('eventEndTime')
    ;[startDateInput, startTimeInput, endTimeInput].forEach((el) => {
      if (el) el.addEventListener('change', () => buildOccurrencesFromForm({ preview: true }))
    })
}

/* =============== TÍNH LỊCH LẶP + SUMMARY BẰNG JS =============== */

function buildOccurrencesFromForm(options = {}) {
  const { preview = false, maxOccurrences = 500 } = options

  const startDateStr = document.getElementById('eventStartDate')?.value || ''
  const startTimeStr = document.getElementById('eventStartTime')?.value || ''
  const endTimeStr = document.getElementById('eventEndTime')?.value || ''

  const repeatType = document.getElementById('eventRepeatType')?.value || ''
  const repeatUntilStr = document.getElementById('eventRepeatUntil')?.value || ''

  const weekdayCheckboxes = Array.from(document.querySelectorAll('.weekday-checkbox:checked'))
  const weekdayValues = weekdayCheckboxes.map((cb) => parseInt(cb.value, 10))

  const weekSummaryEl = document.getElementById('weekSummary')
  if (weekSummaryEl && preview) weekSummaryEl.textContent = ''

  if (!startDateStr || !startTimeStr || !endTimeStr) {
    if (weekSummaryEl && preview) weekSummaryEl.textContent = ''
    return { ok: false, message: 'Thiếu ngày/giờ.' }
  }

  const start = new Date(`${startDateStr}T${startTimeStr}:00`)
  const end = new Date(`${startDateStr}T${endTimeStr}:00`)

  if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
    if (!preview && window.toastr) toastr.error('Ngày/giờ không hợp lệ.')
    return { ok: false }
  }

  if (end <= start) {
    if (!preview && window.toastr) toastr.error('Giờ kết thúc phải sau giờ bắt đầu.')
    return { ok: false, message: 'end-before-start' }
  }

  const baseDurationMs = end.getTime() - start.getTime()

  // Không lặp
  if (!repeatType || !repeatUntilStr) {
    if (preview && weekSummaryEl) {
      weekSummaryEl.textContent = '(Sẽ tạo 1 lịch)'
    }
    return {
      ok: true,
      occurrences: [{ start: new Date(start), end: new Date(end) }]
    }
  }

  const repeatUntil = new Date(repeatUntilStr + 'T23:59:59')
  if (Number.isNaN(repeatUntil.getTime())) {
    if (!preview && window.toastr) toastr.error('Ngày lặp đến không hợp lệ.')
    return { ok: false }
  }
  if (repeatUntil < start) {
    if (!preview && window.toastr) toastr.error('Ngày lặp đến phải sau hoặc bằng ngày bắt đầu.')
    return { ok: false }
  }

  const occ = []

  if (repeatType === 'daily') {
    let cursor = new Date(start)
    while (cursor <= repeatUntil && occ.length < maxOccurrences) {
      const s = new Date(cursor)
      const e = new Date(cursor.getTime() + baseDurationMs)
      occ.push({ start: s, end: e })
      cursor.setDate(cursor.getDate() + 1)
    }
  } else if (repeatType === 'weekly') {
    if (!weekdayValues.length) {
      if (!preview && window.toastr) toastr.error('Hãy chọn ít nhất 1 ngày trong tuần để lặp.')
      return { ok: false }
    }

    let cursor = new Date(start)
    cursor.setHours(0, 0, 0, 0)

    while (cursor <= repeatUntil && occ.length < maxOccurrences) {
      const dow = cursor.getDay()
      if (weekdayValues.includes(dow)) {
        const s = new Date(
          cursor.getFullYear(),
          cursor.getMonth(),
          cursor.getDate(),
          start.getHours(),
          start.getMinutes(),
          start.getSeconds()
        )
        const e = new Date(s.getTime() + baseDurationMs)
        occ.push({ start: s, end: e })
      }
      cursor.setDate(cursor.getDate() + 1)
    }
  } else if (repeatType === 'monthly') {
    let cursor = new Date(start)
    while (cursor <= repeatUntil && occ.length < maxOccurrences) {
      const s = new Date(cursor)
      const e = new Date(cursor.getTime() + baseDurationMs)
      occ.push({ start: s, end: e })

      cursor.setMonth(cursor.getMonth() + 1)
    }
  } else {
    return {
      ok: true,
      occurrences: [{ start: new Date(start), end: new Date(end) }]
    }
  }

  if (!occ.length) {
    if (!preview && window.toastr) toastr.error('Không tạo được lịch nào, vui lòng kiểm tra lại phần lặp.')
    return { ok: false }
  }

  if (preview && weekSummaryEl) {
    weekSummaryEl.textContent = `(Sẽ tạo ${occ.length} lịch)`
  }

  return { ok: true, occurrences: occ }
}

/* =============== CHECK TRÙNG LỊCH LOCAL ================= */

function hasLocalConflict(occurrences, labCode, excludeEventId = null) {
  if (!Array.isArray(occurrences) || !labCode) return false

  const approvedEventsSameRoom = events.filter(
    (e) => e.status === 'approved' && String(e.lab_code || e.roomCode) === String(labCode)
  )

  for (const occ of occurrences) {
    const s1 = occ.start instanceof Date ? occ.start : new Date(occ.start)
    const e1 = occ.end instanceof Date ? occ.end : new Date(occ.end)

    for (const ev of approvedEventsSameRoom) {
      if (excludeEventId && String(ev.id) === String(excludeEventId)) continue

      const s2 = ev.start instanceof Date ? ev.start : new Date(ev.start)
      const e2 = ev.end instanceof Date ? ev.end : new Date(ev.end || ev.start)

      if (s1 < e2 && e1 > s2) {
        return true
      }
    }
  }

  return false
}

/* ======================= LOAD + RENDER EVENTS ======================= */

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
        lab_code: roomCode,
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

/* ======================= MODAL CREATE / EDIT ======================= */

function openCreateModal(start = null, end = null) {
  const form = document.getElementById('eventForm')
  if (form) form.reset()

  document.getElementById('modalTitle').textContent = 'Tạo sự kiện mới'
  document.getElementById('eventId').value = ''

  const now = new Date()
  const today = now.toISOString().split('T')[0]

  const startDateInput = document.getElementById('eventStartDate')
  const startTimeInput = document.getElementById('eventStartTime')
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
      if (endTimeInput) endTimeInput.value = endDate.toTimeString().slice(0, 5)
    } else {
      const endAuto = new Date(startDate.getTime() + 60 * 60 * 1000)
      if (endTimeInput) endTimeInput.value = endAuto.toTimeString().slice(0, 5)
    }
  } else {
    if (startDateInput) startDateInput.value = today
    if (startTimeInput) startTimeInput.value = '09:00'
    if (endTimeInput) endTimeInput.value = '10:00'
  }

  // reset repeat
  const repeatTypeSelect = document.getElementById('eventRepeatType')
  const repeatUntilInput = document.getElementById('eventRepeatUntil')
  const weekdaySection = document.getElementById('weekdaySection')
  const weekSummary = document.getElementById('weekSummary')

  if (repeatTypeSelect) repeatTypeSelect.value = ''
  if (repeatUntilInput) repeatUntilInput.value = ''
  if (weekdaySection) {
    weekdaySection.style.display = 'none'
    document.querySelectorAll('.weekday-checkbox').forEach((cb) => {
      cb.checked = false
    })
  }
  if (weekSummary) weekSummary.textContent = ''

  const modal = document.getElementById('eventModal')
  if (modal) modal.classList.add('active')

  buildOccurrencesFromForm({ preview: true })
}

function closeModal() {
  const modal = document.getElementById('eventModal')
  if (modal) modal.classList.remove('active')
}

/* ======================= SAVE EVENT (CREATE + REPEAT) ======================= */

async function sendBookingRequest(url, formData) {
  const response = await fetch(url, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      Accept: 'application/json'
    }
  })

  const text = await response.text()
  const contentType = response.headers.get('content-type') || ''

  if (!contentType.includes('application/json')) {
    console.error('saveEvent response not JSON:', response.status, text)
    if (window.toastr) toastr.error('Có lỗi xảy ra khi lưu sự kiện (server không trả JSON).')
    return { ok: false }
  }

  let data = null
  try {
    data = JSON.parse(text)
  } catch (e) {
    console.error('saveEvent JSON parse error:', response.status, text)
    if (window.toastr) toastr.error('Có lỗi xảy ra khi lưu sự kiện (response không phải JSON).')
    return { ok: false }
  }

  if (!response.ok) {
    if (window.toastr) toastr.error((data && data.message) || 'Có lỗi xảy ra khi lưu sự kiện.')
    return { ok: false }
  }

  return { ok: true, data }
}

async function saveEvent() {
  const eventId = document.getElementById('eventId').value
  const title = document.getElementById('eventTitle').value.trim()
  const category = document.getElementById('eventCategory').value
  const color = document.getElementById('eventColor').value
  const labCode = "LAB-304";
  const registeredFor = document.getElementById('eventRegisteredFor').value.trim()

  const startDate = document.getElementById('eventStartDate').value
  const startTime = document.getElementById('eventStartTime').value
  const endTime = document.getElementById('eventEndTime').value
  const description = document.getElementById('eventDescription').value.trim()

  const repeatType = document.getElementById('eventRepeatType').value
  const repeatUntil = document.getElementById('eventRepeatUntil').value
  const repeatDays = Array.from(document.querySelectorAll('.weekday-checkbox:checked')).map((cb) => cb.value)

  if (!title || !labCode || !startDate || !startTime || !endTime) {
    if (window.toastr) toastr.error('Vui lòng điền đầy đủ thông tin bắt buộc.')
    return
  }

  // Tính toàn bộ occurrences (JS)
  const occResult = buildOccurrencesFromForm({ preview: false, maxOccurrences: 500 })
  if (!occResult.ok) return

  const occurrences = occResult.occurrences || []
  if (!occurrences.length) {
    if (window.toastr) toastr.error('Không tạo được lịch nào, vui lòng kiểm tra lại phần lặp.')
    return
  }

  // console.log('📅 Occurrences to create:', occurrences.length)

  // Check trùng với events hiện tại (chỉ lịch approved)
  const hasConflict = hasLocalConflict(occurrences, labCode, eventId || null)
  // console.log('⚠️ Has conflict:', hasConflict)

  // if (hasConflict) {
  //   const ok = window.confirm(
  //     'Khung giờ bạn chọn bị trùng với một số lịch ĐÃ DUYỆT trong cùng phòng.\n' +
  //       'Bạn vẫn muốn tiếp tục tạo các lịch này?'
  //   )
  //   if (!ok) return
  // }

  const isEditing = !!eventId
  const baseUrl = isEditing ? `/bookings/${eventId}` : '/bookings'

  try {
    if (isEditing) {

      const start = `${startDate} ${startTime}:00`
      const end = `${startDate} ${endTime}:00`

      const fd = new FormData()
      fd.append('title', title)
      fd.append('category', category)
      fd.append('color', color)
      fd.append('lab_code', labCode)
      fd.append('start', start)
      fd.append('end', end)
      fd.append('description', description)
      fd.append('registered_for', registeredFor)
      fd.append('_method', 'PUT')

      fd.append('repeat_type', repeatType || '')
      fd.append('repeat_until', repeatUntil || '')
      repeatDays.forEach((d) => fd.append('repeat_days[]', d))

      const filesEl = document.getElementById('eventFiles')
      const files = filesEl ? filesEl.files : null
      if (files && files.length > 0) {
        for (let i = 0; i < files.length; i++) fd.append('files[]', files[i])
      }

      const res = await sendBookingRequest(baseUrl, fd)
      if (!res.ok) return

      if (res.data && res.data.message && window.toastr) toastr.success(res.data.message)
    } else {
      // CREATE: tạo N lịch bằng N request
      let successCount = 0
      let failCount = 0

      const filesEl = document.getElementById('eventFiles')
      const files = filesEl ? filesEl.files : null

      console.log(` Starting to create ${occurrences.length} events...`)

      for (let i = 0; i < occurrences.length; i++) {
        const occ = occurrences[i]
        const s = occ.start
        const e = occ.end

        const year = s.getFullYear()
        const month = String(s.getMonth() + 1).padStart(2, '0')
        const day = String(s.getDate()).padStart(2, '0')
        const sh = String(s.getHours()).padStart(2, '0')
        const sm = String(s.getMinutes()).padStart(2, '0')
        const eh = String(e.getHours()).padStart(2, '0')
        const em = String(e.getMinutes()).padStart(2, '0')

        const startStr = `${year}-${month}-${day} ${sh}:${sm}:00`
        const endStr = `${year}-${month}-${day} ${eh}:${em}:00`

        console.log(`Creating event ${i + 1}/${occurrences.length}:`, startStr, '-', endStr)

        const fd = new FormData()
        fd.append('title', title)
        fd.append('category', category)
        fd.append('color', color)
        fd.append('lab_code', labCode)
        fd.append('start', startStr)
        fd.append('end', endStr)
        fd.append('description', description)
        fd.append('registered_for', registeredFor)

        // Đánh dấu đây là lịch lặp và chỉ gửi thông báo ở lần đầu
        fd.append('is_recurring', 'true')
        fd.append('is_first_occurrence', i === 0 ? 'true' : 'false')
        fd.append('total_occurrences', occurrences.length)
        // CHỈ gửi file ở occurrence đầu
        if (i === 0 && files && files.length > 0) {
          for (let j = 0; j < files.length; j++) fd.append('files[]', files[j])
        }

        const res = await sendBookingRequest(baseUrl, fd)
        if (res.ok) {
          successCount++
        } else {
          failCount++
        }
      }

      console.log(` Created ${successCount} events successfully, ${failCount} failed`)

      if (successCount > 0) {
        if (window.toastr) toastr.success(`Đã tạo ${successCount} lịch thành công${failCount > 0 ? ` (${failCount} lịch thất bại)` : ''}.`)
      } else {
        if (window.toastr) toastr.error('Không tạo được lịch nào.')
        return
      }
    }

    closeModal()
    await loadEvent()
  } catch (error) {
    console.error('Error:', error)
    if (window.toastr) toastr.error('Có lỗi xảy ra khi lưu sự kiện.')
  }
}

/* ======================= DETAIL MODAL ======================= */

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
  const cancelledIcon = document.getElementById('statusCancelledIcon')

  if (pendingIcon) pendingIcon.style.display = status === 'pending' ? 'inline' : 'none'
  if (approvedIcon) approvedIcon.style.display = status === 'approved' ? 'inline' : 'none'
  if (completedIcon) completedIcon.style.display = status === 'completed' ? 'inline' : 'none'
  if (cancelledIcon) cancelledIcon.style.display = status === 'cancelled' ? 'inline' : 'none'

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

function editEvent() {
  const eventData = calendar.getEventById(currentEventId)
  if (!eventData) return

  if (!checkPermission(eventData)) {
    if (window.toastr) toastr.error('Bạn không có quyền chỉnh sửa sự kiện này.')
    return
  }

  closeDetailModal()

  document.getElementById('modalTitle').textContent = 'Chỉnh sửa sự kiện'
  document.getElementById('eventId').value = currentEventId
  document.getElementById('eventTitle').value = eventData.title
  document.getElementById('eventCategory').value = eventData.extendedProps.category || 'work'
  document.getElementById('eventRoom').value = eventData.extendedProps.roomCode || 'Lab Phát triển phần mềm và hệ thống thông minh'
  document.getElementById('eventRegisteredFor').value = eventData.extendedProps.registered_for || ''
  document.getElementById('eventDescription').value = eventData.extendedProps.description || ''

  const startDate = new Date(eventData.start)
  const endDate = eventData.end ? new Date(eventData.end) : new Date(startDate.getTime() + 3600000)

  document.getElementById('eventStartDate').value = startDate.toISOString().split('T')[0]
  document.getElementById('eventStartTime').value = startDate.toTimeString().slice(0, 5)
  document.getElementById('eventEndTime').value = endDate.toTimeString().slice(0, 5)

  // Reset repeat
  document.getElementById('eventRepeatType').value = ''
  document.getElementById('eventRepeatUntil').value = ''
  const weekdaySection = document.getElementById('weekdaySection')
  if (weekdaySection) {
    weekdaySection.style.display = 'none'
    document.querySelectorAll('.weekday-checkbox').forEach((cb) => {
      cb.checked = false
    })
  }

  const modal = document.getElementById('eventModal')
  if (modal) modal.classList.add('active')

  buildOccurrencesFromForm({ preview: true })
}

/* ======================= DELETE EVENT ======================= */

function deleteEvent() {
  const eventData = calendar.getEventById(currentEventId)
  if (!eventData) return

  if (!checkPermission(eventData)) {
    if (window.toastr) toastr.error('Bạn không có quyền xóa sự kiện này.')
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

/* ======================= UPDATE TIME BY DRAG/RESIZE ======================= */

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

/* ======================= MINI CALENDAR ======================= */

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

/* ======================= PERMISSION ======================= */

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

/* ======================= BOOTSTRAP ======================= */

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